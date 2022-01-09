<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Cases\Bridge\Nette\DI;

use Tracy\Bar;
use Tester\Assert;
use Tester\TestCase;
use Nette\DI\Container;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\GitVersionPanel;
use SixtyEightPublishers\TracyGitVersion\Tests\Helper\ContainerHelper;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI\TracyGitVersionExtension;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
final class TracyGitVersionExtensionTest extends TestCase
{
	public function testBasicIntegrationInDebugMode(): void
	{
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = $this->createContainer([], TRUE);
		});

		assert($container instanceof Container);

		$bar = $container->getByType(Bar::class);
		assert($bar instanceof Bar);

		Assert::type(GitVersionPanel::class, $bar->getPanel(GitVersionPanel::class));
		Assert::type(GitRepositoryInterface::class, $container->getByType(GitRepositoryInterface::class, FALSE));

		ContainerHelper::clearCache(get_class($container));
	}

	public function testBasicIntegrationWithoutDebugMode(): void
	{
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = $this->createContainer([], FALSE);
		});

		assert($container instanceof Container);

		$bar = $container->getByType(Bar::class);

		assert($bar instanceof Bar);

		Assert::null($bar->getPanel(GitVersionPanel::class));
		Assert::type(GitRepositoryInterface::class, $container->getByType(GitRepositoryInterface::class, FALSE));

		ContainerHelper::clearCache(get_class($container));
	}

	/**
	 * @param array $config
	 * @param bool  $debugMode
	 *
	 * @return \Nette\DI\Container
	 */
	private function createContainer(array $config = [], bool $debugMode = FALSE): Container
	{
		return ContainerHelper::create(array_merge(
			[
			'extensions' => [
				'68publishers.tracy_git_version' => new Statement(TracyGitVersionExtension::class),
			],
			'application' => [
				'scanDirs' => FALSE,
			],
		],
			$config
		), $debugMode);
	}
}

(new TracyGitVersionExtensionTest())->run();
