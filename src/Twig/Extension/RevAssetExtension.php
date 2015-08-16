<?php

namespace Undine\Twig\Extension;

class RevAssetExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @param string $manifestPath Base path for revisioned file manifests.
     */
    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rev_asset';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('rev_asset', [$this, 'getRevAsset']),
        ];
    }

    public function getRevAsset($assetName, $manifestCollection)
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
}
