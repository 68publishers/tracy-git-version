<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;
use function sprintf;

final class BarCommand implements GitCommandInterface
{
	private int $num;

	public function __construct(int $num)
	{
		$this->num = $num;
	}

	public function getNum(): int
	{
		return $this->num;
	}

	public function __toString(): string
	{
		return sprintf('BAR(%d)', $this->num);
	}
}
