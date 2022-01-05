<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel;

use SixtyEightPublishers\TracyGitVersionPanel\Exception\GitDirectoryException;

final class GitDirectory
{
	private ?string $workingDirectory;

	private ?string $workingDirectoryPath = NULL;

	private ?string $gitDirectory = NULL;

	private string $directoryName;

	/**
	 * @param string|NULL $gitDirectory
	 * @param string|NULL $workingDirectory
	 * @param string      $directoryName
	 */
	private function __construct(?string $gitDirectory, ?string $workingDirectory = NULL, string $directoryName = '.git')
	{
		$this->workingDirectory = $workingDirectory;
		$this->gitDirectory = $gitDirectory;
		$this->directoryName = $directoryName;
	}

	/**
	 * @param string $gitDirectory
	 *
	 * @return static
	 * @throws \SixtyEightPublishers\TracyGitVersionPanel\Exception\GitDirectoryException
	 */
	public static function createFromGitDirectory(string $gitDirectory): self
	{
		$realGitDirectory = realpath($gitDirectory);

		if (FALSE === $realGitDirectory || !file_exists($realGitDirectory)) {
			throw GitDirectoryException::invalidGitDirectory($gitDirectory);
		}

		return new self($realGitDirectory, NULL);
	}

	/**
	 * @param string|NULL $workingDirectory
	 * @param string      $directoryName
	 *
	 * @return static
	 */
	public static function createAutoDetected(?string $workingDirectory = NULL, string $directoryName = '.git'): self
	{
		return new self(NULL, $workingDirectory, $directoryName);
	}

	/**
	 * @return string
	 * @throws \SixtyEightPublishers\TracyGitVersionPanel\Exception\GitDirectoryException
	 */
	public function __toString(): string
	{
		if (NULL !== $this->gitDirectory) {
			return $this->gitDirectory;
		}

		$workingDirectory = $this->getWorkingDirectoryPath();

		while (FALSE !== $workingDirectory) {
			$currentDirectory = $workingDirectory;
			$gitDirectory = $workingDirectory . DIRECTORY_SEPARATOR . $this->directoryName;

			if (is_dir($gitDirectory)) {
				return $this->gitDirectory = $gitDirectory;
			}

			$workingDirectory = dirname($workingDirectory) . '';

			if ($workingDirectory === $currentDirectory) {
				break;
			}
		}

		throw GitDirectoryException::gitDirectoryNotFound($this->workingDirectory);
	}

	/**
	 * @return string
	 * @throws \SixtyEightPublishers\TracyGitVersionPanel\Exception\GitDirectoryException
	 */
	private function getWorkingDirectoryPath(): string
	{
		if (NULL !== $this->workingDirectoryPath) {
			return $this->workingDirectoryPath;
		}

		$workingDirectory = $this->workingDirectory ?? dirname($_SERVER['SCRIPT_FILENAME']);
		$workingDirectoryPath = realpath($workingDirectory);

		if (FALSE === $workingDirectoryPath) {
			throw GitDirectoryException::invalidWorkingDirectory($workingDirectory);
		}

		return $this->workingDirectoryPath = $workingDirectoryPath;
	}
}
