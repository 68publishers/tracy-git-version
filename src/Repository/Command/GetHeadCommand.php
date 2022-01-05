<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Command;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandInterface;

final class GetHeadCommand implements GitCommandInterface
{
	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return 'GET_HEAD()';
	}
}
