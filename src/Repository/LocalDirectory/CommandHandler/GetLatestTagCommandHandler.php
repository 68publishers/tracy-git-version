<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\GitDirectory;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand;

final class GetLatestTagCommandHandler implements GitCommandHandlerInterface
{
	private GitDirectory $gitDirectory;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\GitDirectory $gitDirectory
	 */
	public function __construct(GitDirectory $gitDirectory)
	{
		$this->gitDirectory = $gitDirectory;
	}

	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand $getLatestTag
	 *
	 * @return \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Tag|NULL
	 */
	public function __invoke(GetLatestTagCommand $getLatestTag): ?Tag
	{
		$tagsDirectory = $this->gitDirectory . '/refs/tags';

		if (!file_exists($tagsDirectory)) {
			return NULL;
		}

		$latestTagNames = [];
		$latestTimestamp = 0;

		foreach (scandir($tagsDirectory) as $tagName) {
			$filename = $tagsDirectory . '/' . $tagName;

			if (!is_readable($filename)) {
				continue;
			}

			$creationTime = @filectime($filename);

			if (FALSE !== $creationTime && $creationTime >= $latestTimestamp) {
				$latestTimestamp = $creationTime;
				$latestTagNames[$tagName] = $filename;
			}
		}

		if (empty($latestTagNames)) {
			return NULL;
		}

		krsort($latestTagNames);

		return new Tag(key($latestTagNames), new CommitHash(trim((string) @file_get_contents(current($latestTagNames)))));
	}
}
