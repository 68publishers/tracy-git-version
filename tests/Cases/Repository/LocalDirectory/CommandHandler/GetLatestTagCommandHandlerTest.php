<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Repository\LocalDirectory\CommandHandler;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\GitDirectory;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;

require __DIR__ . '/../../../../bootstrap.php';

final class GetLatestTagCommandHandlerTest extends TestCase
{
	public function testCommandHandling(): void
	{
		$handler = new GetLatestTagCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../../files/test-git'));
		$tag = $handler(new GetLatestTagCommand());

		Assert::type(Tag::class, $tag);
		Assert::same('v0.2.1', $tag->getName());
		Assert::same('8f2c308e3a5330b7924634edd7aa38eec97a4114', $tag->getCommitHash()->getValue());
	}

	public function testCommandHandlingWithoutDefinedTags(): void
	{
		$handler = new GetLatestTagCommandHandler(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../../files/test-git-detached'));
		$tag = $handler(new GetLatestTagCommand());

		Assert::null($tag);
	}
}

(new GetLatestTagCommandHandlerTest())->run();
