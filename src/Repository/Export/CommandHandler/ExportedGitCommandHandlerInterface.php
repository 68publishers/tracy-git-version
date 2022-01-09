<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;

interface ExportedGitCommandHandlerInterface extends GitCommandHandlerInterface
{
	/**
	 * @param array $exportedValue
	 *
	 * @return $this
	 */
	public function withExportedValue(array $exportedValue): self;
}
