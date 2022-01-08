<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Bridge\Symfony\Console\Command;

use Tester\Assert;
use Tester\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Exception\ExportConfigException;
use SixtyEightPublishers\TracyGitVersionPanel\Bridge\Symfony\Console\Application;
use SixtyEightPublishers\TracyGitVersionPanel\Bridge\Symfony\Console\Command\ExportRepositoryCommand;

require __DIR__ . '/../../../../../bootstrap.php';

final class ExportRepositoryCommandTest extends TestCase
{
	public function testWithoutArguments() : void
	{
		Assert::exception(function () {
			$this->doTestExecute([]);
		}, ExportConfigException::class, 'Missing the option output_file in export config.');
	}

	public function testWithNonExistentConfig() : void
	{
		Assert::exception(function () {
			$this->doTestExecute([
				'--config' => __DIR__ . '/non-existent-config.php',
			]);
		}, ExportConfigException::class, 'Config file %a%/non-existent-config.php not found.');
	}

	public function testWithCustomConfig() : void
	{
		$commandTester = $this->doTestExecute([
			'--config' => __DIR__ . '/../../../../../files/export/config.php',
		]);

		Assert::same(0, $commandTester->getStatusCode());

		$filename = TEMP_PATH . '/exports/git-repository-export.json';

		$this->doTestExportedFile($filename);
		$this->removeFile($filename);
	}

	public function testWithCustomConfigAndOutputFileOption() : void
	{
		$commandTester = $this->doTestExecute([
			'--config' => __DIR__ . '/../../../../../files/export/config.php',
			'--output-file' => TEMP_PATH . '/exports/git-repository-export-custom.json',
		]);

		Assert::same(0, $commandTester->getStatusCode());

		$filename = TEMP_PATH . '/exports/git-repository-export-custom.json';

		$this->doTestExportedFile($filename);
		$this->removeFile($filename);
	}

	public function testDumpOnly() : void
	{
		$commandTester = $this->doTestExecute([
			'--config' => __DIR__ . '/../../../../../files/export/config.php',
			'--output-file' => TEMP_PATH . '/exports/non-existent-export.json',
			'--dump-only' => TRUE,
		]);

		Assert::same(0, $commandTester->getStatusCode());
		Assert::false(file_exists(TEMP_PATH . '/exports/non-existent-export.json'));
	}

	/**
	 * @param array $arguments
	 *
	 * @return \Symfony\Component\Console\Tester\CommandTester
	 */
	private function doTestExecute(array $arguments): CommandTester
	{
		$application = new Application();
		$application->add(new ExportRepositoryCommand());

		$command = $application->find('export-repository');
		$commandTester = new CommandTester($command);

		$commandTester->execute(
			array_merge(['command' => $command->getName()], $arguments),
			[
				'interactive' => FALSE,
				'decorated' => FALSE,
				'verbosity' => OutputInterface::VERBOSITY_DEBUG,
			]
		);

		return $commandTester;
	}

	/**
	 * @param string $filename
	 *
	 * @throws \Exception
	 */
	private function doTestExportedFile(string $filename) : void
	{
		Assert::true(is_readable($filename));

		$content = @file_get_contents($filename);
		$json = [];

		Assert::type('string', $content);

		Assert::noError(static function () use ($content, &$json) {
			$json = json_decode($content, TRUE, 512, JSON_THROW_ON_ERROR);
		});

		Assert::equal([
			'head' => [
				'branch' => 'master',
				'commit_hash' => '59d7c7f0e34f4db5ba7f5c28e3eb4630339d4382',
			],
			'latest_tag' => [
				'name' => 'v0.2.1',
				'commit_hash' => '8f2c308e3a5330b7924634edd7aa38eec97a4114',
			],
		], $json);
	}

	/**
	 * @param string $filename
	 *
	 * @return void
	 */
	private function removeFile(string $filename) : void
	{
		if (file_exists($filename)) {
			@unlink($filename);
		}
	}
}

(new ExportRepositoryCommandTest())->run();
