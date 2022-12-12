<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Entity;

final class CommitHash
{
	private string $value;

	public function __construct(string $value)
	{
		$this->value = $value;
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function compare(self $commitHash): bool
	{
		return $this->getValue() === $commitHash->getValue();
	}
}
