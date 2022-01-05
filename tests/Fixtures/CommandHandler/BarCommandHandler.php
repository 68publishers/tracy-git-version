<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\BarCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitCommandHandlerInterface;

final class BarCommandHandler implements GitCommandHandlerInterface
{
	public int $callingCounter = 0;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\BarCommand $command
	 *
	 * @return int
	 */
	public function __invoke(BarCommand $command): int
	{
		$this->callingCounter++;

		return $command->getNum() * 2;
	}
}
