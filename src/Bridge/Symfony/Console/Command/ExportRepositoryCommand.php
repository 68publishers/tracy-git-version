<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\LocalDirectoryExporter;
use SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException;

final class ExportRepositoryCommand extends Command
{
	protected static $defaultName = 'export-repository';

	/**
	 * {@inheritdoc}
	 */
	protected function configure(): void
	{
		$this->setDescription('Clones important things from a git repository to the output directory.')
			->addOption('config', NULL, InputOption::VALUE_REQUIRED, 'The path to a config file.')
			->addOption('output-file', NULL, InputOption::VALUE_REQUIRED, 'The filename of the output file.')
			->addOption('dump-only', NULL, InputOption::VALUE_NONE, 'Dumps exported file into the console only.');
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$logger = new ConsoleLogger($output);
		$outputFile = $input->getOption('output-file');
		$dumpOnly = $input->getOption('dump-only');

		$config = $this->getConfig($input->getOption('config'));

		if (is_string($outputFile)) {
			$config->setOutputFile($outputFile);
		}

		$export = (new LocalDirectoryExporter())->export($config, NULL);

		$pretty = $dumpOnly ? JSON_PRETTY_PRINT : 0;
		$json = json_encode($export, JSON_THROW_ON_ERROR | $pretty);

		if ($dumpOnly) {
			$output->writeln($json);

			return 0;
		}

		$outputFile = $config->getOption(Config::OPTION_OUTPUT_FILE);

		$this->writeExport($json, $outputFile);

		$logger->info(sprintf(
			'Repository has been successfully exported into %s.',
			$outputFile
		));

		return 0;
	}

	/**
	 * @param string|NULL $configFile
	 *
	 * @return \SixtyEightPublishers\TracyGitVersion\Export\Config
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	private function getConfig(?string $configFile): Config
	{
		return NULL !== $configFile ? $this->loadConfig($configFile) : Config::createDefault();
	}

	/**
	 * @param string $configFile
	 *
	 * @return \SixtyEightPublishers\TracyGitVersion\Export\Config
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	private function loadConfig(string $configFile): Config
	{
		if (!file_exists($configFile)) {
			throw ExportConfigException::configFileNotFound($configFile);
		}

		/** @noinspection PhpIncludeInspection */
		$config = include $configFile;

		if (!$config instanceof Config) {
			throw ExportConfigException::configCantBeLoadedFromFile($configFile);
		}

		return $config;
	}

	/**
	 * @param string $export
	 * @param string $outputFile
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	private function writeExport(string $export, string $outputFile): void
	{
		$dir = dirname($outputFile);

		if (!is_dir($dir) && !@mkdir($dir, 0777, TRUE) && !is_dir($dir)) {
			throw new RuntimeException(sprintf(
				'Unable to create directory %s',
				$dir
			));
		}

		if (FALSE === @file_put_contents($outputFile, $export)) {
			throw new RuntimeException(sprintf(
				'Unable to write config into file %s',
				$outputFile
			));
		}

		if (FALSE === @chmod($outputFile, 0666)) {
			throw new RuntimeException(sprintf(
				'Unable to chmod config file %s with mode 0666.',
				$outputFile
			));
		}
	}
}
