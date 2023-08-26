<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Bridge\Tracy;

use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\GitVersionPanel;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class GitVersionPanelTest extends TestCase
{
    public function testDefaultPanelRendering(): void
    {
        $panel = GitVersionPanel::createDefault(__DIR__ . '/../../files/nested-directory-1', 'test-git');

        Assert::noError(static function () use ($panel) {
            $panel->getTab();
        });

        Assert::noError(static function () use ($panel) {
            $panel->getPanel();
        });
    }
}

(new GitVersionPanelTest())->run();
