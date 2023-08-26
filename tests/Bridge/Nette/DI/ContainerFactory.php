<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Bridge\Nette\DI;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester\Helpers;
use function debug_backtrace;
use function dirname;
use function sys_get_temp_dir;
use function uniqid;

final class ContainerFactory
{
    private function __construct()
    {
    }

    /**
     * @param string|array<string> $configFiles
     */
    public static function create($configFiles, bool $debugMode): Container
    {
        $tempDir = sys_get_temp_dir() . '/' . uniqid('68publishers:TracyGitVersionPanel', true);
        $backtrace = debug_backtrace();

        Helpers::purge($tempDir);

        $configurator = new Configurator();
        $configurator->setTempDirectory($tempDir);
        $configurator->setDebugMode($debugMode);

        $configurator->addParameters([
            'cwd' => dirname($backtrace[0]['file']),
        ]);

        foreach ((array) $configFiles as $configFile) {
            $configurator->addConfig($configFile);
        }

        return $configurator->createContainer();
    }
}
