<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Repository;

use SixtyEightPublishers\TracyGitVersion\Repository\AbstractGitRepository;

final class SimpleGitRepository extends AbstractGitRepository
{
	private string $source;

	private bool $accessible;

	/**
	 * @param string $source
	 * @param bool   $accessible
	 * @param array  $handlers
	 */
	public function __construct(string $source, bool $accessible, array $handlers = [])
	{
		parent::__construct($handlers);

		$this->source = $source;
		$this->accessible = $accessible;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSource(): string
	{
		return $this->source;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAccessible(): bool
	{
		return $this->accessible;
	}
}
