<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository;

interface GitRepositoryInterface
{
	public const SOURCE_GIT_DIRECTORY = 'git directory';

	/**
	 * @return string
	 */
	public function getSource(): string;

	/**
	 * @return bool
	 */
	public function isAccessible(): bool;

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
