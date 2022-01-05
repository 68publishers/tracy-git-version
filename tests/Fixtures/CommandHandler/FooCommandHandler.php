<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface;

final class FooCommandHandler implements GitCommandHandlerInterface
{
	public int $callingCounter = 0;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand $command
	 *
	 * @return string
	 */
	public function __invoke(FooCommand $command): string
	{
		$this->callingCounter++;

		return 'foo';
	}
}
