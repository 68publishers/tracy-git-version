<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\GitDirectory;

interface LocalDirectoryGitCommandHandlerInterface extends GitCommandHandlerInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\GitDirectory $gitDirectory
	 *
	 * @return $this
	 */
	public function withGitDirectory(GitDirectory $gitDirectory): self;
}
