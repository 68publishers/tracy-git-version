<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Bridge\Tracy;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\Bridge\Tracy\GitVersionPanel;

require __DIR__ . '/../../../bootstrap.php';

final class GitVersionPanelTest extends TestCase
{
	public function testDefaultPanelRendering(): void
	{
		$panel = GitVersionPanel::createDefault(__DIR__ . '/../../../files/nested-directory-1', 'test-git');

		Assert::noError(static function () use ($panel) {
			$panel->getTab();
		});

		Assert::noError(static function () use ($panel) {
			$panel->getPanel();
		});
	}
}

(new GitVersionPanelTest())->run();
