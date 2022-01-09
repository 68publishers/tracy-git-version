<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

interface BlockInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface $gitRepository
	 *
	 * @return string
	 * @throws \Throwable
	 */
	public function render(GitRepositoryInterface $gitRepository): string;
}
