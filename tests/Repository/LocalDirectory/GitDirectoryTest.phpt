<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\LocalDirectory;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use Tester\Assert;
use Tester\TestCase;
use function realpath;
use function sprintf;

require __DIR__ . '/../../bootstrap.php';

final class GitDirectoryTest extends TestCase
{
    public function testExceptionShouldBeThrownOnInvalidWorkingDirectory(): void
    {
        $workingDirectory = __DIR__ . '/non/existent/directory';

        Assert::exception(
            static function () use ($workingDirectory) {
                $_ = (string) GitDirectory::createAutoDetected($workingDirectory);
            },
            GitDirectoryException::class,
            sprintf('The path %s is not valid directory.', $workingDirectory),
        );
    }

    public function testExceptionShouldBeThrownOnInvalidGitDirectory(): void
    {
        $gitDirectory = __DIR__ . '/non/existent/.git';

        Assert::exception(
            static function () use ($gitDirectory) {
                GitDirectory::createFromGitDirectory($gitDirectory);
            },
            GitDirectoryException::class,
            sprintf('The path %s is not valid git directory.', $gitDirectory),
        );
    }

    public function testCreateGitDirectoryDirectly(): void
    {
        $dir = __DIR__ . '/../../files/test-git';
        $gitDirectory = GitDirectory::createFromGitDirectory($dir);

        Assert::same(realpath($dir), (string) $gitDirectory);
    }

    public function testCreateGitDirectoryFromWorkingDirectory(): void
    {
        $realGitDirectoryPath = realpath(__DIR__ . '/../../files/test-git');

        $gitDirectory = GitDirectory::createAutoDetected(__DIR__ . '/../../files', 'test-git');

        Assert::same($realGitDirectoryPath, (string) $gitDirectory);

        $gitDirectory = GitDirectory::createAutoDetected(__DIR__ . '/../../files/nested-directory-1/nested-directory-2', 'test-git');

        Assert::same($realGitDirectoryPath, (string) $gitDirectory);
    }
}

(new GitDirectoryTest())->run();
