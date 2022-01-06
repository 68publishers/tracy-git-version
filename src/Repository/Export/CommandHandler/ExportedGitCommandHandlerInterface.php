<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface;

interface ExportedGitCommandHandlerInterface extends GitCommandHandlerInterface
{
	/**
	 * @param array $exportedValue
	 *
	 * @return $this
	 */
	public function withExportedValue(array $exportedValue): self;
}
