<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Exception;

use Exception;
use function sprintf;

final class GitDirectoryException extends Exception implements ExceptionInterface
{
	public static function invalidWorkingDirectory(string $directory): self
	{
		return new self(sprintf(
			'The path %s is not valid directory.',
			$directory
		));
	}

	public static function gitDirectoryNotProvided(): self
	{
		return new self('Git directory is not provided.');
	}

	public static function invalidGitDirectory(string $directory): self
	{
		return new self(sprintf(
			'The path %s is not valid git directory.',
			$directory
		));
	}

	public static function gitDirectoryNotFound(string $workingDirectory): self
	{
		return new self(sprintf(
			'Git directory from the working directory %s not found.',
			$workingDirectory
		));
	}
}
