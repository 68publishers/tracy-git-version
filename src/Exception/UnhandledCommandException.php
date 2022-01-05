<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Exception;

use Exception;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandInterface;

final class UnhandledCommandException extends Exception implements ExceptionInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandInterface $command
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
