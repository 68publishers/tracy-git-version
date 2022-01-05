<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository;

interface GitRepositoryInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandInterface $command
	 *
	 * @return mixed
	 * @throws \SixtyEightPublishers\TracyGitVersionPanel\Exception\UnhandledCommandException
	 */
	public function handle(GitCommandInterface $command);

	/**
	 * @param string $commandClassname
	 *
	 * @return bool
	 */
	public function supports(string $commandClassname): bool;
}
