<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Export\CommandHandler;

abstract class AbstractExportedCommandHandler implements ExportedGitCommandHandlerInterface
{
	private ?array $exportedValue;

	/**
	 * @param array|NULL $exportedValue
	 */
	public function __construct(?array $exportedValue = NULL)
	{
		$this->exportedValue = $exportedValue;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withExportedValue(array $exportedValue): ExportedGitCommandHandlerInterface
	{
		return new static($exportedValue);
	}

	/**
	 * @return array
	 */
	protected function getExportedValue(): array
	{
		return $this->exportedValue ?? [];
	}
}
