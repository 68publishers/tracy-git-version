<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository\LocalDirectory;

use CzProject\GitPhp\GitRepository;
use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;
use function realpath;
use function sprintf;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class GitDirectoryTest extends TestCase
{
    private GitRepository $repository;

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
        $dir = $this->repository->getRepositoryPath() . '/.git';
        $gitDirectory = GitDirectory::createFromGitDirectory($dir);

        Assert::same(realpath($dir), (string) $gitDirectory);
    }

    public function testCreateGitDirectoryFromWorkingDirectory(): void
    {
        GitHelper::createFile($this->repository, 'nested-directory-1/nested-directory-2/test.txt', '');

        $workingDir = $this->repository->getRepositoryPath();
        $gitDir = '.git';

        $gitDirectory = GitDirectory::createAutoDetected($workingDir, $gitDir);

        Assert::same($workingDir . '/' . $gitDir, (string) $gitDirectory);

        $gitDirectory = GitDirectory::createAutoDetected($workingDir . '/nested-directory-1/nested-directory-2', $gitDir);

        Assert::same($workingDir . '/' . $gitDir, (string) $gitDirectory);
    }

    public function testGitCommandShouldBeExecuted(): void
    {
        GitHelper::createFile($this->repository, 'test.txt', '');
        GitHelper::commit($this->repository, 'first commit');
        GitHelper::createFile($this->repository, 'test2.txt', '');
        GitHelper::commit($this->repository, 'second commit');

        $gitDirectory = GitDirectory::createAutoDetected($this->repository->getRepositoryPath());

        Assert::same([
            'code' => 0,
            'out' => '2',
            'err' => '',
        ], $gitDirectory->executeGitCommand([
            'rev-list',
            '--all',
            '--count',
        ]));
    }

    protected function setUp(): void
    {
        $this->repository = GitHelper::init();
    }

    protected function tearDown(): void
    {
        GitHelper::destroy($this->repository);
    }
}

(new GitDirectoryTest())->run();
