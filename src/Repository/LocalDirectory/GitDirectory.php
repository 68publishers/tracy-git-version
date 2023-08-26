<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use function dirname;
use function file_exists;
use function is_dir;
use function realpath;

final class GitDirectory
{
    private ?string $workingDirectory;

    private ?string $workingDirectoryPath = null;

    private ?string $gitDirectory;

    private string $directoryName;

    private function __construct(?string $gitDirectory, ?string $workingDirectory = null, string $directoryName = '.git')
    {
        $this->workingDirectory = $workingDirectory;
        $this->gitDirectory = $gitDirectory;
        $this->directoryName = $directoryName;
    }

    /**
     * @throws GitDirectoryException
     */
    public static function createFromGitDirectory(string $gitDirectory): self
    {
        $realGitDirectory = realpath($gitDirectory);

        if (false === $realGitDirectory || !file_exists($realGitDirectory)) {
            throw GitDirectoryException::invalidGitDirectory($gitDirectory);
        }

        return new self($realGitDirectory, null);
    }

    public static function createAutoDetected(?string $workingDirectory = null, string $directoryName = '.git'): self
    {
        return new self(null, $workingDirectory, $directoryName);
    }

    /**
     * @throws GitDirectoryException
     */
    public function __toString(): string
    {
        if (null !== $this->gitDirectory) {
            return $this->gitDirectory;
        }

        $workingDirectory = $this->getWorkingDirectoryPath();

        do {
            $currentDirectory = $workingDirectory;
            $gitDirectory = $workingDirectory . DIRECTORY_SEPARATOR . $this->directoryName;

            if (is_dir($gitDirectory)) {
                return $this->gitDirectory = $gitDirectory;
            }

            $workingDirectory = dirname($workingDirectory);
        } while ($workingDirectory !== $currentDirectory);

        throw GitDirectoryException::gitDirectoryNotFound($workingDirectory);
    }

    /**
     * @throws GitDirectoryException
     */
    private function getWorkingDirectoryPath(): string
    {
        if (null !== $this->workingDirectoryPath) {
            return $this->workingDirectoryPath;
        }

        $workingDirectory = $this->workingDirectory ?? dirname($_SERVER['SCRIPT_FILENAME']);
        $workingDirectoryPath = realpath($workingDirectory);

        if (false === $workingDirectoryPath) {
            throw GitDirectoryException::invalidWorkingDirectory($workingDirectory);
        }

        return $this->workingDirectoryPath = $workingDirectoryPath;
    }
}
