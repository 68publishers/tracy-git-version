<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException;
use function assert;
use function get_class;
use function is_callable;

abstract class AbstractGitRepository implements GitRepositoryInterface
{
    /** @var array<GitCommandHandlerInterface> */
    private array $handlers = [];

    /**
     * @param array<class-string, GitCommandHandlerInterface> $handlers
     */
    public function __construct(array $handlers = [])
    {
        foreach ($handlers as $commandClassname => $handler) {
            $this->addHandler($commandClassname, $handler);
        }
    }

    public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
    {
        $this->handlers[$commandClassname] = $handler;
    }

    public function handle(GitCommandInterface $command)
    {
        $classname = get_class($command);

        if (!$this->supports($classname)) {
            throw UnhandledCommandException::cantHandleCommand($command);
        }

        $handler = $this->handlers[$classname];
        assert(is_callable($handler));

        return $handler($command);
    }

    public function supports(string $commandClassname): bool
    {
        return isset($this->handlers[$commandClassname]) && $this->isAccessible();
    }
}
