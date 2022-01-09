<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Cases\Export;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\LocalDirectoryExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;

require __DIR__ . '/../../bootstrap.php';

final class LocalDirectoryExporterTest extends TestCase
{
	public function testWithDefaultConfig(): void
	{
		$exporter = new LocalDirectoryExporter();
		$config = Config::createDefault();

		# directory must be override
		$config->setGitDirectory(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git'));

		$export = $exporter->export($config, NULL);

		Assert::equal([
			'head' => [
				'branch' => 'master',
				'commit_hash' => '59d7c7f0e34f4db5ba7f5c28e3eb4630339d4382',
			],
			'latest_tag' => [
				'name' => 'v0.2.1',
				'commit_hash' => '8f2c308e3a5330b7924634edd7aa38eec97a4114',
			],
		], $export);
	}

	public function testWithDefaultConfigAndDetachedHead(): void
	{
		$exporter = new LocalDirectoryExporter();
		$config = Config::createDefault();

		# directory must be override
		$config->setGitDirectory(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git-detached'));

		$export = $exporter->export($config, NULL);

		Assert::equal([
			'head' => [
				'branch' => NULL,
				'commit_hash' => '3416c5b1831774dd209e489100b3a7c1e333690d',
			],
		], $export);
	}
}

(new LocalDirectoryExporterTest())->run();
