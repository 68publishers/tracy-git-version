<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Export\PartialExporter;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\HeadExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetHeadCommandHandler;

require __DIR__ . '/../../bootstrap.php';

final class HeadExporterTest extends TestCase
{
	public function testExportHead(): void
	{
		$exporter = new HeadExporter();

		$gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git'), [
			GetHeadCommand::class => new GetHeadCommandHandler(),
		]);

		$config = Config::create();

		$export = $exporter->export($config, $gitRepository);

		Assert::equal([
			'head' => [
				'branch' => 'master',
				'commit_hash' => '59d7c7f0e34f4db5ba7f5c28e3eb4630339d4382',
			],
		], $export);
	}

	public function testExportDetachedHead(): void
	{
		$exporter = new HeadExporter();

		$gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git-detached'), [
			GetHeadCommand::class => new GetHeadCommandHandler(),
		]);

		$config = Config::create();

		$export = $exporter->export($config, $gitRepository);

		Assert::equal([
			'head' => [
				'branch' => null,
				'commit_hash' => '3416c5b1831774dd209e489100b3a7c1e333690d',
			],
		], $export);
	}
}

(new HeadExporterTest())->run();
