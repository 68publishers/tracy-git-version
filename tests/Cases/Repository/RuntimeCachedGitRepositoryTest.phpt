<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Repository;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\BarCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\RuntimeCachedGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\FooCommandHandler;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\BarCommandHandler;

require __DIR__ . '/../../bootstrap.php';

final class RuntimeCachedGitRepositoryTest extends TestCase
{
	public function testSupportsMethod() : void
	{
		$repository = new RuntimeCachedGitRepository(new GitRepository([
			FooCommand::class => new FooCommandHandler(),
			BarCommand::class => new BarCommandHandler(),
		]));

		Assert::true($repository->supports(FooCommand::class));
		Assert::true($repository->supports(BarCommand::class));
		Assert::false($repository->supports('BazCommand'));
	}

	public function testCommandHandling() : void
	{
		$fooCommandHandler = new FooCommandHandler();
		$barCommandHandler = new BarCommandHandler();

		$repository = new RuntimeCachedGitRepository(new GitRepository([
			FooCommand::class => $fooCommandHandler,
			BarCommand::class => $barCommandHandler,
		]));

		Assert::same('foo', $repository->handle(new FooCommand()));
		Assert::same(200, $repository->handle(new BarCommand(100)));

		# duplicated calling
		Assert::same('foo', $repository->handle(new FooCommand()));
		Assert::same(200, $repository->handle(new BarCommand(100)));

		# new calling (different argument)
		Assert::same(60, $repository->handle(new BarCommand(30)));

		Assert::same(1, $fooCommandHandler->callingCounter);
		Assert::same(2, $barCommandHandler->callingCounter);
	}
}

(new RuntimeCachedGitRepositoryTest())->run();
