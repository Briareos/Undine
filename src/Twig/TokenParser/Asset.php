<?php

namespace Undine\Twig\TokenParser;

/**
 * The gulp build process revisions (ie. renames) files to file_name-{file_hash}.js
 * Twig should be aware of this change, so gulp dumps the changes it has done to a
 * manifest file in a temporary directory (eg. var/tmp) for twig to pick them up.
 *
 * The manifest files are structured like so:
 *  {
 *      "app.js": "app-a1b2c3d4e5f6.js"
 *  }
 *
 * Because of some gulp-rev project limitations, it is NOT dumped into a single manifest
 * with full paths, although it would be desirable:
 *  {
 *      "js/app.js": "js/app-a1b2c3d4e5f6.js",
 *      "css/app.css": "js/app-a1b2c3d4e5f6.js"
 *  }
 *
 * Hence the need for the "$manifestCollection" parameter in the 'rev_asset' function.
 *
 * See gulpfile.js for available assets and manifest collections.
 */
class Asset extends \Twig_TokenParser
{
    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @param string $manifestPath
     */
    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $stream = $this->parser->getStream();

        $stream->getCurrent()->test('string');
        $assetName = $stream->getCurrent()->getValue();
        $stream->expect(\Twig_Token::STRING_TYPE);
        $assetStore = $stream->next()->getValue();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $asset = $this->extractAssetPath($assetName, $assetStore);

        /** @noinspection PhpParamsInspection */

        return new \Twig_Node_Print(new \Twig_Node_Expression_Constant($asset, $token->getLine()), $token->getLine(), $this->getTag());
    }

    private function extractAssetPath($assetName, $manifestCollection)
    {
        $fileName = $this->manifestPath.'/'.$manifestCollection;
        if (!file_exists($fileName)) {
            throw new \RuntimeException(sprintf('Manifest file "%s" doest not exist.', $fileName));
        }

        $json = file_get_contents($fileName);

        if ($json === false) {
            $error = error_get_last();
            throw new \RuntimeException(sprintf('Unable to read the manifest file "%s": %s', $fileName, $error['message']));
        }

        $manifest = \Undine\Functions\json_parse($json);

        if (!isset($manifest[$assetName])) {
            throw new \RuntimeException(sprintf('The asset named "%s" could not be found in manifest collection "%s"; found assets are: "%s".', $assetName, $manifestCollection, implode('", "', array_keys($manifest))));
        }

        return $manifest[$assetName];
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'asset';
    }
}
