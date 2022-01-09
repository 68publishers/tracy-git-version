<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Command;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;

final class GetLatestTagCommand implements GitCommandInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return 'GET_LATEST_TAG()';
	}
}
