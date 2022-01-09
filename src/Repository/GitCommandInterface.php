<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

interface GitCommandInterface
{
	/**
	 * @return string
	 */
	public function __toString(): string;
}
