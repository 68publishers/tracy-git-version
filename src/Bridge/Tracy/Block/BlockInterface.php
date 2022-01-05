<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface;

interface BlockInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface $gitRepository
	 *
	 * @return string
	 * @throws \Throwable
	 */
	public function render(GitRepositoryInterface $gitRepository): string;
}
