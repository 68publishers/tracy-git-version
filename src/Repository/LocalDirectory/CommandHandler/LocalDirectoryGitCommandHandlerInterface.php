<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;

interface LocalDirectoryGitCommandHandlerInterface extends GitCommandHandlerInterface
{
	public function withGitDirectory(GitDirectory $gitDirectory): self;
}
