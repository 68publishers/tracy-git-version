<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\AbstractExportedCommandHandler;

final class ExportedFooCommandHandler extends AbstractExportedCommandHandler
{
	public int $callingCounter = 0;

	public function __invoke(FooCommand $command): ?string
	{
		$this->callingCounter++;

		return $this->getExportedValue()['foo'] ?? null;
	}
}
