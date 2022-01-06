<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Bridge\Nette\DI;

use Tester\Assert;
use Tester\TestCase;
use RuntimeException;
use Nette\DI\Container;
use Nette\Bootstrap\Configurator;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\TracyGitVersionPanel\Bridge\Nette\DI\TracyGitVersionPanelExtension;
use SixtyEightPublishers\TracyGitVersionPanel\Bridge\Nette\DI\TracyGitVersionPanelExportExtension;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
final class TracyGitVersionPanelExportExtensionTest extends TestCase
{
	public function testBasicIntegration(): void
	{
		Assert::noError(function () use (&$container) {
			$container = $this->createContainer([
				'extensions' => [
					'68publishers.tracy_git_version_panel' => new Statement(TracyGitVersionPanelExtension::class),
					'68publishers.tracy_git_version_panel.export' => new Statement(TracyGitVersionPanelExportExtension::class),
				],
			], TRUE);
		});
	}

	public function testBasicIntegrationWithoutMainExtension(): void
	{
		$container = NULL;

		Assert::exception(function () use (&$container) {
			$container = $this->createContainer([
				'extensions' => [
					'68publishers.tracy_git_version_panel.export' => new Statement(TracyGitVersionPanelExportExtension::class),
				],
			], FALSE);
		}, RuntimeException::class, sprintf('The extension %s can be used only with %s.', TracyGitVersionPanelExportExtension::class, TracyGitVersionPanelExtension::class));
	}

	/**
	 * @param array $config
	 * @param bool  $debugMode
	 *
	 * @return \Nette\DI\Container
	 */
	private function createContainer(array $config = [], bool $debugMode = FALSE): Container
	{
		$configurator = new Configurator();

		$configurator->setTempDirectory(TEMP_PATH . '/TracyGitVersionPanelExtensionTest')
			->setDebugMode($debugMode)
			->addConfig($config);

		return $configurator->createContainer();
	}
}

(new TracyGitVersionPanelExportExtensionTest())->run();
