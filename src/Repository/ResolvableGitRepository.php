<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use SixtyEightPublishers\TracyGitVersion\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException;

final class ResolvableGitRepository implements GitRepositoryInterface
{
	/** @var \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface[]  */
	private array $repositories;

	private bool $resolved = FALSE;

	private ?GitRepositoryInterface $resolvedRepository = NULL;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface[] $repositories
	 */
	public function __construct(array $repositories)
	{
		$this->repositories = (static fn (GitRepositoryInterface ...$repositories): array => $repositories)(...$repositories);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSource(): string
	{
		$repository = $this->getResolvedRepository();

		return NULL !== $repository ? $repository->getSource() : 'unresolved';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAccessible(): bool
	{
		$repository = $this->getResolvedRepository();

		return NULL !== $repository ? $repository->isAccessible() : FALSE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
	{
		throw BadMethodCallException::cantAddHandlerToResolvableGitRepository($commandClassname, get_class($handler));
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(GitCommandInterface $command)
	{
		$repository = $this->getResolvedRepository();

		if (NULL === $repository) {
			throw UnhandledCommandException::cantHandleCommand($command);
		}

		return $repository->handle($command);
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports(string $commandClassname): bool
	{
		$repository = $this->getResolvedRepository();

		return NULL !== $repository ? $repository->supports($commandClassname) : FALSE;
	}

	/**
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface|NULL
	 */
	private function getResolvedRepository(): ?GitRepositoryInterface
	{
		if (FALSE === $this->resolved) {
			foreach ($this->repositories as $repository) {
				if ($repository->isAccessible()) {
					$this->resolvedRepository = $repository;

					break;
				}
			}

			$this->resolved = TRUE;
		}

		return $this->resolvedRepository;
	}
}
