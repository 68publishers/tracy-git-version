<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;

final class GetHeadCommandHandler extends AbstractLocalDirectoryCommandHandler
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand $command
	 *
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException
	 */
	public function __invoke(GetHeadCommand $command): Head
	{
		$headFile = $this->getGitDirectory() . DIRECTORY_SEPARATOR . 'HEAD';

		# not versioned
		if (!is_readable($headFile) || FALSE === ($content = @file_get_contents($headFile))) {
			return new Head(NULL, NULL);
		}

		# detached head
		if (0 !== strpos($content, 'ref:')) {
			return new Head(NULL, new CommitHash(trim($content)));
		}

		$branchParts = explode('/', $content, 3);
		$commitFile = $this->getGitDirectory() . DIRECTORY_SEPARATOR . trim(substr($content, 5, strlen($content)));

		return new Head(
			isset($branchParts[2]) ? trim($branchParts[2]) : NULL,
			is_readable($commitFile) && FALSE !== ($commitHash = @file_get_contents($commitFile)) ? new CommitHash(trim($commitHash)) : NULL
		);
	}
}
