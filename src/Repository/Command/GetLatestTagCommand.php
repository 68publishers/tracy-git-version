<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Command;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandInterface;

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
