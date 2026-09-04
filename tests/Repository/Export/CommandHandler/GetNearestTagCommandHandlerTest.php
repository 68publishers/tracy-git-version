<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetNearestTagCommandHandler;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class GetNearestTagCommandHandlerTest extends TestCase
{
    public function testCommandHandling(): void
    {
        $handler = new GetNearestTagCommandHandler([
            'nearest_tag' => ['name' => 'v0.2.1', 'commit_hash' => '8f2c308e3a5330b7924634edd7aa38eec97a4114', 'distance' => 3],
        ]);

        $nearestTag = $handler(new GetNearestTagCommand());

        Assert::type(NearestTag::class, $nearestTag);
        Assert::same('v0.2.1', $nearestTag->getTag()->getName());
        Assert::same('8f2c308e3a5330b7924634edd7aa38eec97a4114', $nearestTag->getTag()->getCommitHash()->getValue());
        Assert::same(3, $nearestTag->getDistance());
    }

    public function testCommandHandlingWithoutDefinedTag(): void
    {
        $handler = new GetNearestTagCommandHandler([]);

        Assert::null($handler(new GetNearestTagCommand()));
    }
}

(new GetNearestTagCommandHandlerTest())->run();
