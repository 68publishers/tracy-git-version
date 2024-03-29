<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class GetHeadCommandHandlerTest extends TestCase
{
    public function testCommandHandling(): void
    {
        $handler = new GetHeadCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git'));
        $head = $handler(new GetHeadCommand());

        Assert::type(Head::class, $head);
        Assert::same('master', $head->getBranch());
        Assert::same('59d7c7f0e34f4db5ba7f5c28e3eb4630339d4382', $head->getCommitHash()->getValue());
        Assert::false($head->isDetached());
    }

    public function testCommandHandlingUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('commit message');

            $handler = new GetHeadCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $head = $handler(new GetHeadCommand());

            Assert::type(Head::class, $head);
            Assert::same($repository->getCurrentBranchName(), $head->getBranch());
            Assert::same($repository->getLastCommitId()->toString(), $head->getCommitHash()->getValue());
            Assert::false($head->isDetached());
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testCommandHandlingOnDetachedHead(): void
    {
        $handler = new GetHeadCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git-detached'));
        $head = $handler(new GetHeadCommand());

        Assert::type(Head::class, $head);
        Assert::null($head->getBranch());
        Assert::same('3416c5b1831774dd209e489100b3a7c1e333690d', $head->getCommitHash()->getValue());
        Assert::true($head->isDetached());
    }

    public function testCommandHandlingOnDetachedHeadUsingBinary(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('commit message');

            $commitId = $repository->getLastCommitId()->toString();

            GitHelper::createFile($repository, 'file2.txt', 'test 2');
            $repository->commit('commit message 2');
            $repository->checkout($commitId);

            $handler = new GetHeadCommandHandler(GitDirectory::createAutoDetected($repository->getRepositoryPath()), true);
            $head = $handler(new GetHeadCommand());

            Assert::type(Head::class, $head);
            Assert::null($head->getBranch());
            Assert::same($commitId, $head->getCommitHash()->getValue());
            Assert::true($head->isDetached());
        } finally {
            GitHelper::destroy($repository);
        }
    }
}

(new GetHeadCommandHandlerTest())->run();
