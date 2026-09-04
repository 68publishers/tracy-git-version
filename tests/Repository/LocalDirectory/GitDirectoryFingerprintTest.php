<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\LocalDirectory;

use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectoryFingerprint;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;
use function sys_get_temp_dir;
use function uniqid;

require __DIR__ . '/../../bootstrap.php';

final class GitDirectoryFingerprintTest extends TestCase
{
    public function testFingerprintFollowsHeadAndTags(): void
    {
        $repository = GitHelper::init();

        try {
            $fingerprint = new GitDirectoryFingerprint(GitDirectory::createAutoDetected($repository->getRepositoryPath()));

            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');
            $afterFirstCommit = $fingerprint->compute();

            Assert::type('string', $afterFirstCommit);
            Assert::same($afterFirstCommit, $fingerprint->compute());

            GitHelper::createFile($repository, 'file2.txt', 'test');
            $repository->commit('second');
            $afterSecondCommit = $fingerprint->compute();

            Assert::notSame($afterFirstCommit, $afterSecondCommit);

            $repository->createTag('v1.0.0');
            $afterTag = $fingerprint->compute();

            Assert::notSame($afterSecondCommit, $afterTag);
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testFixtureDirectories(): void
    {
        Assert::type('string', (new GitDirectoryFingerprint(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git')))->compute());
        Assert::type('string', (new GitDirectoryFingerprint(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git-detached')))->compute());
    }

    public function testNullWithoutGitDirectory(): void
    {
        $fingerprint = new GitDirectoryFingerprint(GitDirectory::createAutoDetected(sys_get_temp_dir(), uniqid('.no-git-', true)));

        Assert::null($fingerprint->compute());
    }
}

(new GitDirectoryFingerprintTest())->run();
