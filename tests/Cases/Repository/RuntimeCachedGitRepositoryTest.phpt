<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Cases\Repository;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\BarCommand;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\RuntimeCachedGitRepository;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Repository\SimpleGitRepository;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler\FooCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler\LocalDirectoryFooCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler\BarCommandHandler;

require __DIR__ . '/../../bootstrap.php';

final class RuntimeCachedGitRepositoryTest extends TestCase
{
	public function testRepositorySource() : void
	{
		$repository = $this->createRepository([]);

		Assert::same('test', $repository->getSource());
	}

	public function testIsAccessibleMethod() : void
	{
		$repository = $this->createRepository([]);

		Assert::true($repository->isAccessible());
	}

	public function testSupportsMethod() : void
	{
		$repository = $this->createRepository([
			FooCommand::class => new FooCommandHandler(),
			BarCommand::class => new BarCommandHandler(),
		]);

		Assert::true($repository->supports(FooCommand::class));
		Assert::true($repository->supports(BarCommand::class));
		Assert::false($repository->supports('BazCommand'));
	}

	public function testCommandHandling() : void
	{
		$fooCommandHandler = new FooCommandHandler();
		$barCommandHandler = new BarCommandHandler();

		$repository = $this->createRepository([
			FooCommand::class => $fooCommandHandler,
			BarCommand::class => $barCommandHandler,
		]);

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

	/**
	 * @param array $handlers
	 *
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\RuntimeCachedGitRepository
	 */
	private function createRepository(array $handlers) : RuntimeCachedGitRepository
	{
		return new RuntimeCachedGitRepository(new SimpleGitRepository('test', TRUE, $handlers));
	}
}

(new RuntimeCachedGitRepositoryTest())->run();
