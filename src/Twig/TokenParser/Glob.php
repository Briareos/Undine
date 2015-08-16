<?php

namespace Undine\Twig\TokenParser;

use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Twig_Error_Syntax;
use Twig_Token;
use Undine\Twig\Node\GlobNode;

class Glob extends \Twig_TokenParser
{
    private $baseDir;

    /**
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }


    /**
     * {@inheritdoc}
     */
    public function parse(Twig_Token $token)
    {
        $dirs = [];
        $name = null;
        $output = null;
        $stream = $this->parser->getStream();
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                // 'web/css', 'web/js'
                $dirs[] = $stream->next()->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'pattern')) {
                // patern='\.js$'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $pattern = $stream->next()->getValue();
            } else {
                $token = $stream->getCurrent();
                throw new \Twig_Error_Syntax(sprintf('Unexpected token "%s" of value "%s"', \Twig_Token::typeToEnglish($token->getType()), $token->getValue()), $token->getLine(), $stream->getFilename());
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'testEndTag'], true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $matches = $this->matchPatterns($this->baseDir, $dirs, $pattern);

        return new GlobNode($matches, ['body' => $body], [], $token->getLine(), $this->getTag());
    }

    /**
     * @param string          $baseDir Root directory.
     * @param string[]|string $dirs    List of globs.
     *
     * @return string[] List of files that match the globs.
     */
    private function matchPatterns($baseDir, $dirs, $pattern)
    {
        $files = [];

        if (is_array($dirs)) {
            foreach ($dirs as $dir) {
                $files += $this->matchPatterns($baseDir, $dir, $pattern);
            }
        } else {
            $dirLength = strlen($baseDir.$dirs);
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseDir.$dirs, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                /** @var \SplFileInfo $file */
                if (!$file->isFile()) {
                    continue;
                }
                $pathname = substr($file->getPathname(), $dirLength);
                if (preg_match($pattern, $pathname)) {
                    $files[] = $pathname;
                }
            }
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'glob';
    }

    public function testEndTag(\Twig_Token $token)
    {
        return $token->test(['end'.$this->getTag()]);
    }
}
