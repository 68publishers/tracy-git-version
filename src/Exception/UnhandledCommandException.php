<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Exception;

use Exception;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;

final class UnhandledCommandException extends Exception implements ExceptionInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface $command
	 *
	 * @return static
	 */
	public static function cantHandleCommand(GitCommandInterface $command): self
	{
		return new self(sprintf(
			'Can\'t handle git command %s.',
			$command
		));
	}
}
