<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Bridge\Nette\DI;

use Tester\Assert;
use Tester\TestCase;
use RuntimeException;
use Nette\DI\Container;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Helper\ContainerHelper;
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
		$container = NULL;

		Assert::noError(function () use (&$container) {
			$container = ContainerHelper::create([
				'extensions' => [
					'68publishers.tracy_git_version_panel' => new Statement(TracyGitVersionPanelExtension::class),
					'68publishers.tracy_git_version_panel.export' => new Statement(TracyGitVersionPanelExportExtension::class),
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
					'68publishers.tracy_git_version_panel.export' => new Statement(TracyGitVersionPanelExportExtension::class),
				],
				'application' => [
					'scanDirs' => FALSE,
				],
			], FALSE);
		}, RuntimeException::class, sprintf('The extension %s can be used only with %s.', TracyGitVersionPanelExportExtension::class, TracyGitVersionPanelExtension::class));
	}
}

(new TracyGitVersionPanelExportExtensionTest())->run();
