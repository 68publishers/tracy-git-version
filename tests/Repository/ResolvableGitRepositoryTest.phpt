<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Repository;

use SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException;
use SixtyEightPublishers\TracyGitVersion\Repository\ResolvableGitRepository;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\BarCommand;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler\BarCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler\FooCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Repository\SimpleGitRepository;
use Tester\Assert;
use Tester\TestCase;
use function sprintf;

require __DIR__ . '/../bootstrap.php';

final class ResolvableGitRepositoryTest extends TestCase
{
    public function testRepositorySource(): void
    {
        # first is accessible
        $repository = $this->createRepository([
            new SimpleGitRepository('first', true, []),
            new SimpleGitRepository('second', true, []),
        ]);

        Assert::same('first', $repository->getSource());

        # first is not accessible
        $repository = $this->createRepository([
            new SimpleGitRepository('first', false, []),
            new SimpleGitRepository('second', true, []),
        ]);

        Assert::same('second', $repository->getSource());

        # unresolved state
        $repository = $this->createRepository([
            new SimpleGitRepository('first', false, []),
            new SimpleGitRepository('second', false, []),
        ]);

        Assert::same('unresolved', $repository->getSource());

        $repository = $this->createRepository([]);

        Assert::same('unresolved', $repository->getSource());
    }

    public function testIsAccessibleMethod(): void
    {
        # first is accessible
        $repository = $this->createRepository([
            new SimpleGitRepository('first', true, []),
            new SimpleGitRepository('second', true, []),
        ]);

        Assert::true($repository->isAccessible());

        # second is accessible
        $repository = $this->createRepository([
            new SimpleGitRepository('first', false, []),
            new SimpleGitRepository('second', true, []),
        ]);

        Assert::true($repository->isAccessible());

        # none is accessible
        $repository = $this->createRepository([
            new SimpleGitRepository('first', false, []),
            new SimpleGitRepository('second', false, []),
        ]);

        Assert::false($repository->isAccessible());

        $repository = $this->createRepository([]);

        Assert::false($repository->isAccessible());
    }

    public function testSupportsMethod(): void
    {
        $repository = $this->createRepository([
            new SimpleGitRepository('first', true, [FooCommand::class => new FooCommandHandler()]),
            new SimpleGitRepository('second', true, [BarCommand::class => new BarCommandHandler()]),
        ]);

        Assert::true($repository->supports(FooCommand::class));
        Assert::false($repository->supports(BarCommand::class)); # false because the first one is accessible
        Assert::false($repository->supports('BazCommand'));
    }

    public function testCommandHandling(): void
    {
        $repository = $this->createRepository([
            new SimpleGitRepository('first', true, [BarCommand::class => new BarCommandHandler()]),
            new SimpleGitRepository('second', true, [FooCommand::class => new FooCommandHandler()]),
        ]);

        Assert::same(200, $repository->handle(new BarCommand(100)));

        # FooCommand must be unhandled because the first repository doesn't supports it
        $unhandledCommand = new FooCommand();

        Assert::exception(
            static function () use ($repository, $unhandledCommand) {
                $repository->handle($unhandledCommand);
            },
            UnhandledCommandException::class,
            sprintf('Can\'t handle git command %s.', $unhandledCommand),
        );
    }

    private function createRepository(array $repositories): ResolvableGitRepository
    {
        return new ResolvableGitRepository($repositories);
    }
}

(new ResolvableGitRepositoryTest())->run();
