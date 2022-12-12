<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

final class RuntimeCachedGitRepository implements GitRepositoryInterface
{
	private GitRepositoryInterface $inner;

	/** @var array<string, mixed> */
	private array $cache = [];

	public function __construct(GitRepositoryInterface $inner)
	{
		$this->inner = $inner;
	}

	public function getSource(): string
	{
		return $this->inner->getSource();
	}

	public function isAccessible(): bool
	{
		return $this->inner->isAccessible();
	}

	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
	{
		$this->inner->addHandler($commandClassname, $handler);
	}

	public function handle(GitCommandInterface $command)
	{
		$commandId = (string) $command;

		return $this->cache[$commandId] ?? $this->cache[$commandId] = $this->inner->handle($command);
	}

	public function supports(string $commandClassname): bool
	{
		return $this->inner->supports($commandClassname);
	}
}
