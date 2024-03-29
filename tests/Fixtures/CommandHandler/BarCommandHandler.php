<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\BarCommand;

final class BarCommandHandler implements GitCommandHandlerInterface
{
    public int $callingCounter = 0;

    public function __invoke(BarCommand $command): int
    {
        $this->callingCounter++;

        return $command->getNum() * 2;
    }
}
