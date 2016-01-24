<?php

namespace Undine\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ScriptHandler
{
    protected static $options = [
        'symfony-app-dir' => 'app',
        'symfony-bin-dir' => 'bin',
        'symfony-web-dir' => 'web',
    ];

    protected static function getOptions(Event $event)
    {
        return array_merge(static::$options, $event->getComposer()->getPackage()->getExtra());
    }

    public static function install(Event $event)
    {
        self::checkPrerequisites($event);
    }

    public static function installPhantomJsBinary(Event $event)
    {
        $options = self::getOptions($event);
        $binDir = $options['symfony-bin-dir'];

        $fs = new Filesystem();
        $fs->copy(__DIR__.'/../Thumbnail/Resources/phantomjs-capture.js', $binDir.'/phantomjs-capture.js', true);
    }

    public static function checkPrerequisites(Event $event)
    {
        self::verifyPhantomJsVersion();
        self::verifyNpmVersion();
        self::verifyNodeVersion();
    }

    public static function verifyPhantomJsVersion()
    {
        $process = new Process('phantomjs -v');
        $process->run();

        if (!$process->isSuccessful()) {
            $exception = new ProcessFailedException($process);
            throw new \RuntimeException("The phantomjs binary could not be executed successfully. Details:\n".$exception->getMessage());
        }

        $version = trim($process->getOutput());

        if (!preg_match('{^2\.}', $version)) {
            throw new \RuntimeException(sprintf('The required phantomjs version is 2.*.*, found version %s.', $version));
        }
    }

    public static function verifyNpmVersion()
    {
        $process = new Process('npm -v');
        $process->run();

        if (!$process->isSuccessful()) {
            $exception = new ProcessFailedException($process);
            throw new \RuntimeException("The npm binary could not be executed successfully. Details:\n".$exception->getMessage());
        }

        $version = trim($process->getOutput());

        if (!preg_match('{^2\.}', $version)) {
            throw new \RuntimeException(sprintf('The required npm version is 2.*.*, found version %s.', $version));
        }
    }

    public static function verifyNodeVersion()
    {
        $process = new Process('node -v');
        $process->run();

        if (!$process->isSuccessful()) {
            $exception = new ProcessFailedException($process);
            throw new \RuntimeException("The npm binary could not be executed successfully. Details:\n".$exception->getMessage());
        }

        $version = ltrim(trim($process->getOutput()), 'v');

        if (!preg_match('{^4\.}', $version)) {
            throw new \RuntimeException(sprintf('The required npm version is 4.*.*, found version %s.', $version));
        }
    }
}
