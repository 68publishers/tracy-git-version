<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Helpers;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class SimpleTableBlock implements BlockInterface
{
	/** @var array<string, string> */
	private array $rows;

	private ?string $caption;

	/**
	 * @param array<string, string> $rows
	 */
	public function __construct(array $rows, ?string $caption = null)
	{
		$this->rows = $rows;
		$this->caption = $caption;
	}

	public function render(GitRepositoryInterface $gitRepository): string
	{
		return Helpers::renderTemplate(__DIR__ . '/../templates/SimpleTable.block.phtml', [
			'caption' => $this->caption,
			'rows' => $this->rows,
		]);
	}
}
