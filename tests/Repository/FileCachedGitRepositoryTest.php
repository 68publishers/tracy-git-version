<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository;

use Nette\Utils\FileSystem;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\FileCachedGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectoryFingerprint;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CountingGitRepository;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\FooResult;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;
use function chmod;
use function glob;
use function is_dir;
use function sys_get_temp_dir;
use function uniqid;

require __DIR__ . '/../bootstrap.php';

final class FileCachedGitRepositoryTest extends TestCase
{
    public function testResultsSurviveRequestsAndFollowRepositoryState(): void
    {
        $repository = GitHelper::init();
        $cacheDirectory = sys_get_temp_dir() . '/' . uniqid('tgv-cache-', true);

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');

            $inner = new CountingGitRepository(static fn (): NearestTag => new NearestTag(new Tag('v1.0.0', new CommitHash('8f2c308e3a5330b7924634edd7aa38eec97a4114')), 2));
            $fingerprint = new GitDirectoryFingerprint(GitDirectory::createAutoDetected($repository->getRepositoryPath()));

            $cached = new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory);
            $first = $cached->handle(new GetNearestTagCommand());
            $second = $cached->handle(new GetNearestTagCommand());

            Assert::same(1, $inner->calls);
            Assert::same($first, $second);
            Assert::count(1, glob($cacheDirectory . '/tgv-*.cache') ?: []);

            # a new instance stands for the next request: the result comes from the file, not from git
            $nextRequest = new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory);
            $restored = $nextRequest->handle(new GetNearestTagCommand());

            Assert::same(1, $inner->calls);
            Assert::type(NearestTag::class, $restored);
            Assert::same('v1.0.0', $restored->getTag()->getName());
            Assert::same(2, $restored->getDistance());

            # a new tag changes the fingerprint, the stale file is replaced and git is asked again
            $repository->createTag('v1.1.0');
            (new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory))->handle(new GetNearestTagCommand());

            Assert::same(2, $inner->calls);
            Assert::count(1, glob($cacheDirectory . '/tgv-*.cache') ?: []);
        } finally {
            GitHelper::destroy($repository);
            FileSystem::delete($cacheDirectory);
        }
    }

    public function testResultComputedWhileTheStateChangedIsNotStored(): void
    {
        $repository = GitHelper::init();
        $cacheDirectory = sys_get_temp_dir() . '/' . uniqid('tgv-cache-', true);

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');

            # the tag appears while the inner repository is "running git", so the result belongs to the new state
            $inner = new CountingGitRepository(static function () use ($repository): ?NearestTag {
                $repository->createTag('v1.0.0');

                return null;
            });
            $fingerprint = new GitDirectoryFingerprint(GitDirectory::createAutoDetected($repository->getRepositoryPath()));

            (new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory))->handle(new GetNearestTagCommand());

            Assert::same([], glob($cacheDirectory . '/tgv-*.cache') ?: []);
        } finally {
            GitHelper::destroy($repository);
            FileSystem::delete($cacheDirectory);
        }
    }

    public function testIncompleteClassIsTreatedAsMiss(): void
    {
        $repository = GitHelper::init();
        $cacheDirectory = sys_get_temp_dir() . '/' . uniqid('tgv-cache-', true);

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');

            $fingerprint = new GitDirectoryFingerprint(GitDirectory::createAutoDetected($repository->getRepositoryPath()));
            $inner = new CountingGitRepository(static fn (): FooResult => new FooResult());

            # the result class is not in the allowed list of the reading instance
            (new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory, [FooResult::class]))->handle(new GetNearestTagCommand());
            $result = (new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory))->handle(new GetNearestTagCommand());

            Assert::type(FooResult::class, $result);
            Assert::same(2, $inner->calls);
        } finally {
            GitHelper::destroy($repository);
            FileSystem::delete($cacheDirectory);
        }
    }

    public function testUnwritableDirectoryIsSkippedSilently(): void
    {
        $repository = GitHelper::init();
        $cacheDirectory = sys_get_temp_dir() . '/' . uniqid('tgv-cache-', true);

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');
            FileSystem::createDir($cacheDirectory, 0555);

            $inner = new CountingGitRepository(static fn (): ?NearestTag => null);
            $cached = new FileCachedGitRepository($inner, new GitDirectoryFingerprint(GitDirectory::createAutoDetected($repository->getRepositoryPath())), $cacheDirectory);

            Assert::noError(static function () use ($cached): void {
                $cached->handle(new GetNearestTagCommand());
            });
            Assert::same([], glob($cacheDirectory . '/*') ?: []);
        } finally {
            GitHelper::destroy($repository);
            @chmod($cacheDirectory, 0755);
            FileSystem::delete($cacheDirectory);
        }
    }

    public function testTransparentWithoutGitDirectory(): void
    {
        $inner = new CountingGitRepository(static fn (): ?NearestTag => null);
        $fingerprint = new GitDirectoryFingerprint(GitDirectory::createAutoDetected(sys_get_temp_dir(), uniqid('.no-git-', true)));
        $cacheDirectory = sys_get_temp_dir() . '/' . uniqid('tgv-cache-', true);

        $cached = new FileCachedGitRepository($inner, $fingerprint, $cacheDirectory);
        $cached->handle(new GetNearestTagCommand());
        $cached->handle(new GetNearestTagCommand());

        Assert::same(2, $inner->calls);
        Assert::false(is_dir($cacheDirectory));
    }
}

(new FileCachedGitRepositoryTest())->run();
