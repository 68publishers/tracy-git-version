<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Bridge\Nette\DI;

use Tracy\Bar;
use Tester\Assert;
use Tester\TestCase;
use Nette\DI\Container;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\GitVersionPanel;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use function assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class TracyGitVersionExtensionTest extends TestCase
{
	public function testBasicIntegrationInDebugMode(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.main.neon', true);

		$bar = $container->getByType(Bar::class);
		assert($bar instanceof Bar);

		Assert::type(GitVersionPanel::class, $bar->getPanel(GitVersionPanel::class));
		Assert::type(GitRepositoryInterface::class, $container->getByType(GitRepositoryInterface::class, false));
	}

	public function testBasicIntegrationWithoutDebugMode(): void
	{
		$container = ContainerFactory::create(__DIR__ . '/config.main.neon', false);

		assert($container instanceof Container);

		$bar = $container->getByType(Bar::class);

		assert($bar instanceof Bar);

		Assert::null($bar->getPanel(GitVersionPanel::class));
		Assert::type(GitRepositoryInterface::class, $container->getByType(GitRepositoryInterface::class, false));
	}
}

(new TracyGitVersionExtensionTest())->run();
