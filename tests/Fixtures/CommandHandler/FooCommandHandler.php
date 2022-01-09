<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;

final class FooCommandHandler implements GitCommandHandlerInterface
{
	public int $callingCounter = 0;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand $command
	 *
	 * @return string
	 */
	public function __invoke(FooCommand $command): string
	{
		$this->callingCounter++;

		return 'foo';
	}
}
