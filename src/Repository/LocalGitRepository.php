<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\LocalDirectoryGitCommandHandlerInterface;

final class LocalGitRepository extends AbstractGitRepository
{
	private GitDirectory $gitDirectory;

	private string $source;

	/**
	 * @param string                                                                       $source
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory $gitDirectory
	 * @param array                                                                        $handlers
	 */
	public function __construct(GitDirectory $gitDirectory, array $handlers = [], string $source = self::SOURCE_GIT_DIRECTORY)
	{
		$this->gitDirectory = $gitDirectory;
		$this->source = $source;

		parent::__construct($handlers);
	}

	/**
	 * @param string|NULL $workingDirectory
	 * @param string      $directoryName
	 *
	 * @return static
	 */
	public static function createDefault(?string $workingDirectory = NULL, string $directoryName = '.git'): self
	{
		return new self(
			GitDirectory::createAutoDetected($workingDirectory, $directoryName),
			[
				GetHeadCommand::class => new GetHeadCommandHandler(),
				GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
			]
		);
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
