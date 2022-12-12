<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use Throwable;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

interface BlockInterface
{
	/**
	 * @throws Throwable
	 */
	public function render(GitRepositoryInterface $gitRepository): string;
}
