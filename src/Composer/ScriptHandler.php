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

    private static $minimumPhantomJsVersion = '2.0.0';

    protected static function getOptions(Event $event)
    {
        return array_merge(static::$options, $event->getComposer()->getPackage()->getExtra());
    }

    public static function install(Event $event)
    {
//        self::installPhantomJsBinary($event);
        self::checkPrerequisites($event);
    }

    public static function installPhantomJsBinary(Event $event)
    {
        $options = self::getOptions($event);
        $binDir  = $options['symfony-bin-dir'];

        $fs = new Filesystem();
        $fs->copy(__DIR__.'/../Thumbnail/Resources/phantomjs-capture.js', $binDir.'/phantomjs-capture.js', true);
    }

    public static function checkPrerequisites(Event $event)
    {
        self::verifyPhantomJsVersion();
        self::verifyNpmExists();
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

        if (version_compare($version, self::$minimumPhantomJsVersion, '<')) {
            throw new \RuntimeException(sprintf('The minimum phantomjs version is %s, found version %s.', self::$minimumPhantomJsVersion, $version));
        }
    }

    public static function verifyNpmExists()
    {
        $process = new Process('npm -v');
        $process->run();

        if (!$process->isSuccessful()) {
            $exception = new ProcessFailedException($process);
            throw new \RuntimeException("Could not find the npm binary. Details:\n".$exception->getMessage());
        }
    }
}
