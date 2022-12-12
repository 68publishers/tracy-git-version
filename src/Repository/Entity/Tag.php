<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Entity;

final class Tag
{
	private string $name;

	private CommitHash $commitHash;

	public function __construct(string $name, CommitHash $commitHash)
	{
		$this->name = $name;
		$this->commitHash = $commitHash;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getCommitHash(): CommitHash
	{
		return $this->commitHash;
	}
}
