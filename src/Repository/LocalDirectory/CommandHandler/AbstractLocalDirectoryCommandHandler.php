<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;

abstract class AbstractLocalDirectoryCommandHandler implements LocalDirectoryGitCommandHandlerInterface
{
    private ?GitDirectory $gitDirectory;

    public function __construct(?GitDirectory $gitDirectory = null)
    {
        $this->gitDirectory = $gitDirectory;
    }

    public function withGitDirectory(GitDirectory $gitDirectory): LocalDirectoryGitCommandHandlerInterface
    {
        $handler = clone $this;
        $handler->gitDirectory = $gitDirectory;

        return $handler;
    }

    /**
     * @throws GitDirectoryException
     */
    protected function getGitDirectory(): GitDirectory
    {
        if (null === $this->gitDirectory) {
            throw GitDirectoryException::gitDirectoryNotProvided();
        }

        return $this->gitDirectory;
    }
}
