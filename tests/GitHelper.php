<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests;

use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitRepository;
use Nette\Utils\FileSystem;
use function ltrim;
use function rtrim;
use function sys_get_temp_dir;
use function uniqid;

final class GitHelper
{
    public static function init(): GitRepository
    {
        $git = new Git();
        $tempDir = sys_get_temp_dir() . '/' . uniqid('68publishers:TracyGitVersionPanel:GitDirectoryTest', true);
        FileSystem::createDir($tempDir);

        $repo = $git->init($tempDir);
        $repo->execute('config', 'user.email', 'test@68publishers.io');
        $repo->execute('config', 'user.name', 'Test SixtyEightPublishers');

        return $repo;
    }

    public static function destroy(GitRepository $repository): void
    {
        FileSystem::delete($repository->getRepositoryPath());
    }

    public static function createFile(GitRepository $repository, string $name, string $contents): void
    {
        $filename = rtrim($repository->getRepositoryPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($name, DIRECTORY_SEPARATOR);

        FileSystem::write($filename, $contents);

        $repository->addFile($name);
    }

    public static function commit(GitRepository $repository, string $commitMessage): void
    {
        $repository->commit($commitMessage);
    }

    public static function createTag(GitRepository $repository, string $tag): void
    {
        $repository->createTag($tag);
    }
}
