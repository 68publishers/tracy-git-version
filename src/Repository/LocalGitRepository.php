<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository;

use SixtyEightPublishers\TracyGitVersionPanel\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler\LocalDirectoryGitCommandHandlerInterface;

final class LocalGitRepository extends AbstractGitRepository
{
	private GitDirectory $gitDirectory;

	private string $source;

	/**
	 * @param string                                                                            $source
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\GitDirectory $gitDirectory
	 * @param array                                                                             $handlers
	 */
	public function __construct(GitDirectory $gitDirectory, array $handlers = [], string $source = self::SOURCE_GIT_DIRECTORY)
	{
		$this->gitDirectory = $gitDirectory;
		$this->source = $source;

		parent::__construct($handlers);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSource(): string
	{
		return $this->source;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAccessible(): bool
	{
		try {
			$this->gitDirectory->__toString();

			return TRUE;
		} catch (GitDirectoryException $e) {
			return FALSE;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
	{
		if ($handler instanceof LocalDirectoryGitCommandHandlerInterface) {
			$handler = $handler->withGitDirectory($this->gitDirectory);
		}

		parent::addHandler($commandClassname, $handler);
	}
}
