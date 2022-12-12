<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

abstract class AbstractExportedCommandHandler implements ExportedGitCommandHandlerInterface
{
	/** @var array<mixed, mixed>|null */
	private ?array $exportedValue;

	/**
	 * @param array<mixed, mixed>|null $exportedValue
	 */
	public function __construct(?array $exportedValue = null)
	{
		$this->exportedValue = $exportedValue;
	}

	public function withExportedValue(array $exportedValue): ExportedGitCommandHandlerInterface
	{
		$handler = clone $this;
		$handler->exportedValue = $exportedValue;

		return $handler;
	}

	/**
	 * @return array<mixed, mixed>
	 */
	protected function getExportedValue(): array
	{
		return $this->exportedValue ?? [];
	}
}
