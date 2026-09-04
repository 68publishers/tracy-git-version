<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetNearestTagCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class GetNearestTagCommandHandlerTest extends TestCase
{
    public function testHeadOnTagUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('commit message');
            $commitId = $repository->getLastCommitId();
            $repository->createTag('v1.0.0');

            $handler = new GetNearestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $nearestTag = $handler(new GetNearestTagCommand());

            Assert::type(NearestTag::class, $nearestTag);
            Assert::same('v1.0.0', $nearestTag->getTag()->getName());
            Assert::same($commitId->toString(), $nearestTag->getTag()->getCommitHash()->getValue());
            Assert::same(0, $nearestTag->getDistance());
            Assert::true($nearestTag->isExact());
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testHeadAheadOfTagUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');
            $repository->createTag('v1.0.0');
            $taggedCommitId = $repository->getLastCommitId();

            GitHelper::createFile($repository, 'file2.txt', 'test');
            $repository->commit('second');
            GitHelper::createFile($repository, 'file3.txt', 'test');
            $repository->commit('third');

            $handler = new GetNearestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $nearestTag = $handler(new GetNearestTagCommand());

            Assert::type(NearestTag::class, $nearestTag);
            Assert::same('v1.0.0', $nearestTag->getTag()->getName());
            Assert::same($taggedCommitId->toString(), $nearestTag->getTag()->getCommitHash()->getValue());
            Assert::same(2, $nearestTag->getDistance());
            Assert::false($nearestTag->isExact());
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testNewerTagOnAnotherBranchIsIgnoredUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');
            $repository->createTag('v1.0.0');
            $taggedCommitId = $repository->getLastCommitId();

            GitHelper::createFile($repository, 'feature.txt', 'test');
            $repository->commit('feature work');
            $featureBranch = $repository->getCurrentBranchName();

            # a newer release tag on a branch that the feature branch does not contain
            $repository->createBranch('release', true);
            $repository->checkout('release');
            GitHelper::createFile($repository, 'release.txt', 'test');
            $repository->commit('release');
            $repository->createTag('v2.0.0');
            $repository->checkout($featureBranch);

            $handler = new GetNearestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $nearestTag = $handler(new GetNearestTagCommand());

            Assert::type(NearestTag::class, $nearestTag);
            Assert::same('v1.0.0', $nearestTag->getTag()->getName());
            Assert::same($taggedCommitId->toString(), $nearestTag->getTag()->getCommitHash()->getValue());
            Assert::same(1, $nearestTag->getDistance());
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testAnnotatedTagResolvesToTaggedCommitUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');
            $commitId = $repository->getLastCommitId();
            $repository->execute('tag', '-a', 'v1.0.0', '-m', 'release 1.0.0');

            $handler = new GetNearestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $nearestTag = $handler(new GetNearestTagCommand());

            Assert::type(NearestTag::class, $nearestTag);
            Assert::same($commitId->toString(), $nearestTag->getTag()->getCommitHash()->getValue());
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testWithoutTagsUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('commit message');

            $handler = new GetNearestTagCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);

            Assert::null($handler(new GetNearestTagCommand()));
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testWithoutBinaryReturnsNull(): void
    {
        $handler = new GetNearestTagCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git'));

        Assert::null($handler(new GetNearestTagCommand()));
    }
}

(new GetNearestTagCommandHandlerTest())->run();
