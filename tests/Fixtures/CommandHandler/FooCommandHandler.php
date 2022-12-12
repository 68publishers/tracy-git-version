<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;

final class FooCommandHandler implements GitCommandHandlerInterface
{
	public int $callingCounter = 0;

	public function __invoke(FooCommand $command): string
	{
		$this->callingCounter++;

		return 'foo';
	}
}
