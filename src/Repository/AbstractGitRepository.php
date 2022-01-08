<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository;

use SixtyEightPublishers\TracyGitVersionPanel\Exception\UnhandledCommandException;

abstract class AbstractGitRepository implements GitRepositoryInterface
{
	/** @var \SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface[] */
	private array $handlers = [];

	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface[] $handlers
	 */
	public function __construct(array $handlers = [])
	{
		foreach ($handlers as $commandClassname => $handler) {
			$this->addHandler($commandClassname, $handler);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
	{
		$this->handlers[$commandClassname] = $handler;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(GitCommandInterface $command)
	{
		$classname = get_class($command);

		if (!$this->supports($classname)) {
			throw UnhandledCommandException::cantHandleCommand($command);
		}

		$handler = $this->handlers[$classname];

		return $handler($command);
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports(string $commandClassname): bool
	{
		return isset($this->handlers[$commandClassname]) && $this->isAccessible();
	}
}
