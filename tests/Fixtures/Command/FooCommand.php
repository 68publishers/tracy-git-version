<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandInterface;

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
