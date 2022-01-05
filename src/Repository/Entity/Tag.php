<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity;

final class Tag
{
	private string $name;

	private CommitHash $commitHash;

	/**
	 * @param string                                                                  $name
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\CommitHash $commitHash
	 */
	public function __construct(string $name, CommitHash $commitHash)
	{
		$this->name = $name;
		$this->commitHash = $commitHash;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\CommitHash
	 */
	public function getCommitHash(): CommitHash
	{
		return $this->commitHash;
	}
}
