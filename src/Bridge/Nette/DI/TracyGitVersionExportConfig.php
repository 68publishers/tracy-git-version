<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI;

use Nette\DI\Definitions\Statement;

final class TracyGitVersionExportConfig
{
	public string $source_name;

	public string $export_filename;

	/** @var array<Statement> */
	public array $command_handlers;
}
