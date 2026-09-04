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

            # re-pointing an existing tag within the same second keeps names and mtimes, only the ref content changes
            $repository->execute('tag', '-f', 'v1.0.0', 'HEAD~1');
            $afterRepoint = $fingerprint->compute();

            Assert::notSame($afterTag, $afterRepoint);

            # packing refs moves the tag from a loose file into packed-refs, the state itself is unchanged in meaning
            # but the fingerprint may change; what matters is that it is still computed and stable afterwards
            $repository->execute('pack-refs', '--all');

            Assert::same($fingerprint->compute(), $fingerprint->compute());
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
