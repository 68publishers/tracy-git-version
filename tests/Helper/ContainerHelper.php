<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Helper;

use Nette\DI\Container;
use Nette\Utils\Finder;
use Nette\Bootstrap\Configurator;

final class ContainerHelper
{
	private function __construct()
	{
	}

	/**
	 * @param array $config
	 * @param bool  $debugMode
	 *
	 * @return \Nette\DI\Container
	 */
	public static function create(array $config, bool $debugMode): Container
	{
		$configurator = new Configurator();

		$configurator->setTempDirectory(TEMP_PATH)
			->setDebugMode($debugMode)
			->addConfig($config);

		return $configurator->createContainer();
	}

	/**
	 * @param string $containerClassname
	 *
	 * @return void
	 */
	public static function clearCache(string $containerClassname): void
	{
		/** @var \SplFileInfo $file */
		foreach (Finder::findFiles($containerClassname . '*')->from(TEMP_PATH . '/cache') as $file) {
			($path = $file->getRealPath()) && @unlink($path);
		}
	}
}
