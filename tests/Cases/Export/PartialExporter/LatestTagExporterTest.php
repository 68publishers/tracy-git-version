<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Export\PartialExporter;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\Export\Config;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersionPanel\Export\PartialExporter\LatestTagExporter;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;

require __DIR__ . '/../../../bootstrap.php';

final class LatestTagExporterTest extends TestCase
{
	public function testExportLatestTag(): void
	{
		$exporter = new LatestTagExporter();

		$gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git'), [
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

		$gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../../files/test-git-detached'), [
			GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
		]);

		$config = Config::create();

		$export = $exporter->export($config, $gitRepository);

		Assert::equal([], $export);
	}
}

(new HeadExporterTest())->run();
