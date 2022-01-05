<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Repository;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\BarCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Exception\UnhandledCommandException;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\FooCommandHandler;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\BarCommandHandler;

require __DIR__ . '/../../bootstrap.php';

final class GitRepositoryTest extends TestCase
{
	public function testSupportsMethod() : void
	{
		$repository = new GitRepository([
			FooCommand::class => new FooCommandHandler(),
			BarCommand::class => new BarCommandHandler(),
		]);

		Assert::true($repository->supports(FooCommand::class));
		Assert::true($repository->supports(BarCommand::class));
		Assert::false($repository->supports('BazCommand'));
	}

	public function testCommandHandling() : void
	{
		$repository = new GitRepository([
			FooCommand::class => new FooCommandHandler(),
			BarCommand::class => new BarCommandHandler(),
		]);

		Assert::same('foo', $repository->handle(new FooCommand()));
		Assert::same(200, $repository->handle(new BarCommand(100)));
	}

	public function testExceptionShouldBeThrownWhenCommandHasNoHandler() : void
	{
		$repository = new GitRepository([
			FooCommand::class => new FooCommandHandler(),
		]);

		$command = new BarCommand(5);

		Assert::exception(
			static function () use ($repository, $command) {
				$repository->handle($command);
			},
			UnhandledCommandException::class,
			sprintf('Can\'t handle git command %s.', $command)
		);
	}
}

(new GitRepositoryTest())->run();
