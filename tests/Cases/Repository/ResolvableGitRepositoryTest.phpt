<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Repository;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\BarCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\ResolvableGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Exception\UnhandledCommandException;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\RuntimeCachedGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Repository\SimpleGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\FooCommandHandler;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\LocalDirectoryFooCommandHandler;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler\BarCommandHandler;

require __DIR__ . '/../../bootstrap.php';

final class ResolvableGitRepositoryTest extends TestCase
{
	public function testRepositorySource() : void
	{
		# first is accessible
		$repository = $this->createRepository([
			new SimpleGitRepository('first', TRUE, []),
			new SimpleGitRepository('second', TRUE, []),
		]);

		Assert::same('first', $repository->getSource());

		# first is not accessible
		$repository = $this->createRepository([
			new SimpleGitRepository('first', FALSE, []),
			new SimpleGitRepository('second', TRUE, []),
		]);

		Assert::same('second', $repository->getSource());

		# unresolved state
		$repository = $this->createRepository([
			new SimpleGitRepository('first', FALSE, []),
			new SimpleGitRepository('second', FALSE, []),
		]);

		Assert::same('unresolved', $repository->getSource());

		$repository = $this->createRepository([]);

		Assert::same('unresolved', $repository->getSource());
	}

	public function testIsAccessibleMethod() : void
	{
		# first is accessible
		$repository = $this->createRepository([
			new SimpleGitRepository('first', TRUE, []),
			new SimpleGitRepository('second', TRUE, []),
		]);

		Assert::true($repository->isAccessible());

		# second is accessible
		$repository = $this->createRepository([
			new SimpleGitRepository('first', FALSE, []),
			new SimpleGitRepository('second', TRUE, []),
		]);

		Assert::true($repository->isAccessible());

		# none is accessible
		$repository = $this->createRepository([
			new SimpleGitRepository('first', FALSE, []),
			new SimpleGitRepository('second', FALSE, []),
		]);

		Assert::false($repository->isAccessible());

		$repository = $this->createRepository([]);

		Assert::false($repository->isAccessible());
	}

	public function testSupportsMethod() : void
	{
		$repository = $this->createRepository([
			new SimpleGitRepository('first', TRUE, [FooCommand::class => new FooCommandHandler()]),
			new SimpleGitRepository('second', TRUE, [BarCommand::class => new BarCommandHandler()]),
		]);

		Assert::true($repository->supports(FooCommand::class));
		Assert::false($repository->supports(BarCommand::class)); # false because the first one is accessible
		Assert::false($repository->supports('BazCommand'));
	}

	public function testCommandHandling() : void
	{
		$repository = $this->createRepository([
			new SimpleGitRepository('first', TRUE, [BarCommand::class => new BarCommandHandler()]),
			new SimpleGitRepository('second', TRUE, [FooCommand::class => new FooCommandHandler()]),
		]);

		Assert::same(200, $repository->handle(new BarCommand(100)));

		# FooCommand must be unhandled because the first repository doesn't supports it
		$unhandledCommand = new FooCommand();

		Assert::exception(
			static function () use ($repository, $unhandledCommand) {
				$repository->handle($unhandledCommand);
			},
			UnhandledCommandException::class,
			sprintf('Can\'t handle git command %s.', $unhandledCommand)
		);
	}

	/**
	 * @param array $repositories
	 *
	 * @return \SixtyEightPublishers\TracyGitVersionPanel\Repository\ResolvableGitRepository
	 */
	private function createRepository(array $repositories) : ResolvableGitRepository
	{
		return new ResolvableGitRepository($repositories);
	}
}

(new ResolvableGitRepositoryTest())->run();
