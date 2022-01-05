<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository;

interface GitCommandInterface
{
	/**
	 * @return string
	 */
	public function __toString(): string;
}
