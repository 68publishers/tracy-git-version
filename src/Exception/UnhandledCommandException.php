<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Exception;

use Exception;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;
use function sprintf;

final class UnhandledCommandException extends Exception implements ExceptionInterface
{
	public static function cantHandleCommand(GitCommandInterface $command): self
	{
		return new self(sprintf(
			'Can\'t handle git command %s.',
			$command
		));
	}
}
