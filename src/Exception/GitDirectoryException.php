<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Exception;

use Exception;

final class GitDirectoryException extends Exception implements ExceptionInterface
{
	/**
	 * @param string $directory
	 *
	 * @return static
	 */
	public static function invalidWorkingDirectory(string $directory): self
	{
		return new self(sprintf(
			'The path %s is not valid directory.',
			$directory
		));
	}

	/**
	 * @return static
	 */
	public static function gitDirectoryNotProvided(): self
	{
		return new self('Git directory is not provided.');
	}

	/**
	 * @param string $directory
	 *
	 * @return static
	 */
	public static function invalidGitDirectory(string $directory): self
	{
		return new self(sprintf(
			'The path %s is not valid git directory.',
			$directory
		));
	}

	/**
	 * @param string $workingDirectory
	 *
	 * @return static
	 */
	public static function gitDirectoryNotFound(string $workingDirectory): self
	{
		return new self(sprintf(
			'Git directory from the working directory %s not found.',
			$workingDirectory
		));
	}
}
