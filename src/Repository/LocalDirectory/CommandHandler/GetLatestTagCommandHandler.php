<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use function current;
use function file_exists;
use function file_get_contents;
use function filectime;
use function in_array;
use function is_readable;
use function key;
use function krsort;
use function scandir;
use function sprintf;
use function trim;

final class GetLatestTagCommandHandler extends AbstractLocalDirectoryCommandHandler
{
    private bool $useBinary;

    public function __construct(?GitDirectory $gitDirectory = null, bool $useBinary = false)
    {
        parent::__construct($gitDirectory);

        $this->useBinary = $useBinary;
    }

    /**
     * @throws GitDirectoryException
     */
    public function __invoke(GetLatestTagCommand $getLatestTag): ?Tag
    {
        return $this->useBinary ? $this->readUsingBinary() : $this->readFromDirectory();
    }

    /**
     * @throws GitDirectoryException
     */
    private function readFromDirectory(): ?Tag
    {
        $tagsDirectory = sprintf('%s%srefs%stags', $this->getGitDirectory(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);

        if (!file_exists($tagsDirectory)) {
            return null;
        }

        $latestTagNames = [];
        $latestTimestamp = 0;
        $tagNames = scandir($tagsDirectory);

        foreach ($tagNames ?: [] as $tagName) {
            if (in_array($tagName, ['.', '..'], true)) {
                continue;
            }

            $filename = $tagsDirectory . DIRECTORY_SEPARATOR . $tagName;

            if (!is_readable($filename)) {
                continue;
            }

            $creationTime = @filectime($filename);

            if (false !== $creationTime && $creationTime >= $latestTimestamp) {
                $latestTimestamp = $creationTime;
                $latestTagNames[$tagName] = $filename;
            }
        }

        if (empty($latestTagNames)) {
            return null;
        }

        krsort($latestTagNames);

        return new Tag((string) key($latestTagNames), new CommitHash(trim((string) @file_get_contents(current($latestTagNames)))));
    }

    /**
     * @throws GitDirectoryException
     */
    private function readUsingBinary(): ?Tag
    {
        $tagOutput = $this->getGitDirectory()->executeGitCommand([
            'describe',
            '--tags',
            '$(' . $this->getGitDirectory()->createGitCommand([
                'rev-list',
                '--tags',
                '--max-count',
                '1',
            ]) . ')',
        ]);

        if (0 !== $tagOutput['code']) {
            return null;
        }

        $tag = $tagOutput['out'];

        $commitOutput = $this->getGitDirectory()->executeGitCommand([
            'show-ref',
            '-s',
            $tag,
        ]);

        if (0 !== $commitOutput['code']) {
            return null;
        }

        return new Tag($tag, new CommitHash($commitOutput['out']));
    }
}
