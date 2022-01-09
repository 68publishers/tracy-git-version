<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

interface GitRepositoryInterface
{
	public const SOURCE_GIT_DIRECTORY = 'git directory';
	public const SOURCE_EXPORT = 'export';

	/**
	 * @return string
	 */
	public function getSource(): string;

	/**
	 * @return bool
	 */
	public function isAccessible(): bool;

	/**
	 * @param string                                                                      $commandClassname
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface $handler
	 *
	 * @return void
	 */
	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface $command
	 *
	 * @return mixed
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException
	 */
	public function handle(GitCommandInterface $command);

	/**
	 * @param string $commandClassname
	 *
	 * @return bool
	 */
	public function supports(string $commandClassname): bool;
}
