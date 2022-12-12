<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

interface GitRepositoryInterface
{
	public const SOURCE_GIT_DIRECTORY = 'git directory';
	public const SOURCE_EXPORT = 'export';

	public function getSource(): string;

	public function isAccessible(): bool;

	/**
	 * @param class-string $commandClassname
	 */
	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void;

	/**
	 * @return mixed
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException
	 */
	public function handle(GitCommandInterface $command);

	/**
	 * @param class-string $commandClassname
	 */
	public function supports(string $commandClassname): bool;
}
