<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\GitDirectory;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface;

final class GetHeadCommandHandler implements GitCommandHandlerInterface
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
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetHeadCommand $command
	 *
	 * @return \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Head
	 */
	public function __invoke(GetHeadCommand $command): Head
	{
		$headFile = $this->gitDirectory . '/HEAD';

		# not versioned
		if (!is_readable($headFile) || FALSE === ($content = @file_get_contents($headFile))) {
			return new Head(NULL, NULL);
		}

		# detached head
		if (0 !== strpos($content, 'ref:')) {
			return new Head(NULL, new CommitHash(trim($content)));
		}

		$branchParts = explode('/', $content, 3);
		$commitFile = $this->gitDirectory . '/' . trim(substr($content, 5, strlen($content)));

		return new Head(
			isset($branchParts[2]) ? trim($branchParts[2]) : NULL,
			is_readable($commitFile) && FALSE !== ($commitHash = @file_get_contents($commitFile)) ? new CommitHash(trim($commitHash)) : NULL
		);
	}
}
