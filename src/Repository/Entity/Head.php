<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Entity;

final class Head
{
	private ?string $branch;

	private ?CommitHash $commitHash;

	/**
	 * @param string|NULL                                                             $branch
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash|NULL $commitHash
	 */
	public function __construct(?string $branch, ?CommitHash $commitHash)
	{
		$this->branch = $branch;
		$this->commitHash = $commitHash;
	}

	/**
	 * @return string|NULL
	 */
	public function getBranch(): ?string
	{
		return $this->branch;
	}

	/**
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash|NULL
	 */
	public function getCommitHash(): ?CommitHash
	{
		return $this->commitHash;
	}

	/**
	 * @return bool
	 */
	public function isDetached(): bool
	{
		return NULL === $this->branch && NULL !== $this->commitHash;
	}
}
