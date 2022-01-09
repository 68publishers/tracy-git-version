<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;

abstract class AbstractLocalDirectoryCommandHandler implements LocalDirectoryGitCommandHandlerInterface
{
	private ?GitDirectory $gitDirectory;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory|NULL $gitDirectory
	 */
	public function __construct(?GitDirectory $gitDirectory = NULL)
	{
		$this->gitDirectory = $gitDirectory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withGitDirectory(GitDirectory $gitDirectory): LocalDirectoryGitCommandHandlerInterface
	{
		return new static($gitDirectory);
	}

	/**
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException
	 */
	protected function getGitDirectory(): GitDirectory
	{
		if (NULL === $this->gitDirectory) {
			throw GitDirectoryException::gitDirectoryNotProvided();
		}

		return $this->gitDirectory;
	}
}
