<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Cases\Bridge\Nette\DI;

use Tester\Assert;
use Tester\TestCase;
use RuntimeException;
use Nette\DI\Container;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\TracyGitVersion\Tests\Helper\ContainerHelper;
use SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI\TracyGitVersionExtension;
use SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI\TracyGitVersionExportExtension;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
final class TracyGitVersionExportExtensionTest extends TestCase
{
	public function testBasicIntegration(): void
	{
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = ContainerHelper::create([
				'extensions' => [
					'68publishers.tracy_git_version' => new Statement(TracyGitVersionExtension::class),
					'68publishers.tracy_git_version.export' => new Statement(TracyGitVersionExportExtension::class),
				],
				'application' => [
					'scanDirs' => FALSE,
				],
			], TRUE);
		});

		assert($container instanceof Container);
		ContainerHelper::clearCache(get_class($container));
	}

	public function testBasicIntegrationWithoutMainExtension(): void
	{
		$container = NULL;

		Assert::exception(function () use (&$container) {
			$container = ContainerHelper::create([
				'extensions' => [
					'68publishers.tracy_git_version.export' => new Statement(TracyGitVersionExportExtension::class),
				],
				'application' => [
					'scanDirs' => FALSE,
				],
			], FALSE);
		}, RuntimeException::class, sprintf('The extension %s can be used only with %s.', TracyGitVersionExportExtension::class, TracyGitVersionExtension::class));
	}
}

(new TracyGitVersionExportExtensionTest())->run();
