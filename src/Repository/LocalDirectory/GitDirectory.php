<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use function array_merge;
use function dirname;
use function fclose;
use function file_exists;
use function implode;
use function is_dir;
use function is_resource;
use function proc_close;
use function proc_open;
use function realpath;
use function stream_get_contents;
use function trim;

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
     * @param array<int, string> $command
     */
    public function createGitCommand(array $command): string
    {
        $gitDir = (string) $this;
        $parts = array_merge(
            [
                'git',
                '--git-dir',
                $gitDir,
                '--work-tree',
                dirname($gitDir),
            ],
            $command,
        );

        return implode(' ', $parts);
    }

    /**
     * @param array<int, string> $command
     *
     * @return array{
     *     code: int,
     *     out: string,
     *     err: string,
     * }
     */
    public function executeGitCommand(array $command): array
    {
        $process = proc_open(
            $this->createGitCommand($command),
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            __DIR__,
            null,
        );

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        return [
            'code' => is_resource($process) ? proc_close($process) : 1,
            'out' => trim((string) $stdout),
            'err' => trim((string) $stderr),
        ];
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
