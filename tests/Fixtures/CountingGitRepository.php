<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class CountingGitRepository implements GitRepositoryInterface
{
    public int $calls = 0;

    /** @var callable */
    private $factory;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function getSource(): string
    {
        return 'counting';
    }

    public function isAccessible(): bool
    {
        return true;
    }

    public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
    {
    }

    public function handle(GitCommandInterface $command)
    {
        $this->calls++;

        return ($this->factory)($command);
    }

    public function supports(string $commandClassname): bool
    {
        return true;
    }
}
