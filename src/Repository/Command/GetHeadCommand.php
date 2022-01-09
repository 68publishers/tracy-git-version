<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Command;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;

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
