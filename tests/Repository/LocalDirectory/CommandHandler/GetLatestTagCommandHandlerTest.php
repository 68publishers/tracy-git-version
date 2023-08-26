<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class GetLatestTagCommandHandlerTest extends TestCase
{
    public function testCommandHandling(): void
    {
        $handler = new GetLatestTagCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git'));
        $tag = $handler(new GetLatestTagCommand());

        Assert::type(Tag::class, $tag);
        Assert::same('v0.2.1', $tag->getName());
        Assert::same('8f2c308e3a5330b7924634edd7aa38eec97a4114', $tag->getCommitHash()->getValue());
    }

    public function testCommandHandlingUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('commit message');

            $commitId = $repository->getLastCommitId();

            $repository->createTag('v1.0.0');

            $handler = new GetLatestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $tag = $handler(new GetLatestTagCommand());

            Assert::type(Tag::class, $tag);
            Assert::same('v1.0.0', $tag->getName());
            Assert::same($commitId->toString(), $tag->getCommitHash()->getValue());
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testCommandHandlingWithoutDefinedTags(): void
    {
        $handler = new GetLatestTagCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git-detached'));
        $tag = $handler(new GetLatestTagCommand());

        Assert::null($tag);
    }

    public function testCommandHandlingWithoutDefinedTagsUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('commit message');

            $handler = new GetLatestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $tag = $handler(new GetLatestTagCommand());

            Assert::null($tag);
        } finally {
            GitHelper::destroy($repository);
        }
    }
}

(new GetLatestTagCommandHandlerTest())->run();
