<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;

final class FooCommand implements GitCommandInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return 'FOO()';
	}
}
