<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Repository;

use SixtyEightPublishers\TracyGitVersion\Repository\AbstractGitRepository;

final class SimpleGitRepository extends AbstractGitRepository
{
	private string $source;

	private bool $accessible;

	public function __construct(string $source, bool $accessible, array $handlers = [])
	{
		parent::__construct($handlers);

		$this->source = $source;
		$this->accessible = $accessible;
	}

	public function getSource(): string
	{
		return $this->source;
	}

	public function isAccessible(): bool
	{
		return $this->accessible;
	}
}
