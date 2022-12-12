<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use SixtyEightPublishers\TracyGitVersion\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException;
use function get_class;

final class ResolvableGitRepository implements GitRepositoryInterface
{
	/** @var array<GitRepositoryInterface>  */
	private array $repositories;

	private bool $resolved = false;

	private ?GitRepositoryInterface $resolvedRepository = null;

	/**
	 * @param array<GitRepositoryInterface> $repositories
	 */
	public function __construct(array $repositories)
	{
		$this->repositories = (static fn (GitRepositoryInterface ...$repositories): array => $repositories)(...$repositories);
	}

	public function getSource(): string
	{
		$repository = $this->getResolvedRepository();

		return null !== $repository ? $repository->getSource() : 'unresolved';
	}

	public function isAccessible(): bool
	{
		$repository = $this->getResolvedRepository();

		return null !== $repository ? $repository->isAccessible() : false;
	}

	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
	{
		throw BadMethodCallException::cantAddHandlerToResolvableGitRepository($commandClassname, get_class($handler));
	}

	public function handle(GitCommandInterface $command)
	{
		$repository = $this->getResolvedRepository();

		if (null === $repository) {
			throw UnhandledCommandException::cantHandleCommand($command);
		}

		return $repository->handle($command);
	}

	public function supports(string $commandClassname): bool
	{
		$repository = $this->getResolvedRepository();

		return null !== $repository && $repository->supports($commandClassname);
	}

	private function getResolvedRepository(): ?GitRepositoryInterface
	{
		if (false === $this->resolved) {
			foreach ($this->repositories as $repository) {
				if ($repository->isAccessible()) {
					$this->resolvedRepository = $repository;

					break;
				}
			}

			$this->resolved = true;
		}

		return $this->resolvedRepository;
	}
}
