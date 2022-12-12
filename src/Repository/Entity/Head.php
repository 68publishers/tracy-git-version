<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Entity;

final class Head
{
	private ?string $branch;

	private ?CommitHash $commitHash;

	public function __construct(?string $branch, ?CommitHash $commitHash)
	{
		$this->branch = $branch;
		$this->commitHash = $commitHash;
	}

	public function getBranch(): ?string
	{
		return $this->branch;
	}

	public function getCommitHash(): ?CommitHash
	{
		return $this->commitHash;
	}

	public function isDetached(): bool
	{
		return null === $this->branch && null !== $this->commitHash;
	}
}
