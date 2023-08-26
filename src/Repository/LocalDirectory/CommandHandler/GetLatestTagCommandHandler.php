<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
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
    /**
     * @throws GitDirectoryException
     */
    public function __invoke(GetLatestTagCommand $getLatestTag): ?Tag
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
}
