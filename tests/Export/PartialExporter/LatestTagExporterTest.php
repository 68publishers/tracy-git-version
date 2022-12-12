<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Export\PartialExporter;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\LatestTagExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;

require __DIR__ . '/../../bootstrap.php';

final class LatestTagExporterTest extends TestCase
{
	public function testExportLatestTag(): void
	{
		$exporter = new LatestTagExporter();

		$gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git'), [
			GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
		]);

		$config = Config::create();

		$export = $exporter->export($config, $gitRepository);

		Assert::equal([
			'latest_tag' => [
				'name' => 'v0.2.1',
				'commit_hash' => '8f2c308e3a5330b7924634edd7aa38eec97a4114',
			],
		], $export);
	}

	public function testExportWithoutTag(): void
	{
		$exporter = new LatestTagExporter();

		$gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git-detached'), [
			GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
		]);

		$config = Config::create();

		$export = $exporter->export($config, $gitRepository);

		Assert::equal([], $export);
	}
}

(new LatestTagExporterTest())->run();
