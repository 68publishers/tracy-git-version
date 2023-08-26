<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console\Command;

use JsonException;
use RuntimeException;
use SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\LocalDirectoryExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use function assert;
use function chmod;
use function dirname;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function is_string;
use function json_encode;
use function mkdir;
use function sprintf;

final class ExportRepositoryCommand extends Command
{
    protected static $defaultName = 'export-repository';

    protected function configure(): void
    {
        $this->setDescription('Clones important things from a git repository to the output directory.')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'The path to a config file.')
            ->addOption('output-file', null, InputOption::VALUE_REQUIRED, 'The filename of the output file.')
            ->addOption('dump-only', null, InputOption::VALUE_NONE, 'Dumps exported file into the console only.');
    }

    /**
     * @throws JsonException
     * @throws ExportConfigException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $outputFile = $input->getOption('output-file');
        $dumpOnly = $input->getOption('dump-only');

        $configPath = $input->getOption('config');
        assert(null === $configPath || is_string($configPath));
        $config = $this->getConfig($configPath);

        if (is_string($outputFile)) {
            $config->setOutputFile($outputFile);
        }

        $export = (new LocalDirectoryExporter())->export($config, null);

        $pretty = $dumpOnly ? JSON_PRETTY_PRINT : 0;
        $json = json_encode($export, JSON_THROW_ON_ERROR | $pretty);

        if ($dumpOnly) {
            $output->writeln($json);

            return 0;
        }

        $outputFile = $config->getOption(Config::OPTION_OUTPUT_FILE);
        assert(is_string($outputFile));

        $this->writeExport($json, $outputFile);

        $logger->info(sprintf(
            'Repository has been successfully exported into %s.',
            $outputFile,
        ));

        return 0;
    }

    /**
     * @throws ExportConfigException
     */
    private function getConfig(?string $configFile): Config
    {
        return null !== $configFile ? $this->loadConfig($configFile) : Config::createDefault();
    }

    /**
     * @throws ExportConfigException
     */
    private function loadConfig(string $configFile): Config
    {
        if (!file_exists($configFile)) {
            throw ExportConfigException::configFileNotFound($configFile);
        }

        $config = include $configFile;

        if (!$config instanceof Config) {
            throw ExportConfigException::configCantBeLoadedFromFile($configFile);
        }

        return $config;
    }

    /**
     * @throws RuntimeException
     */
    private function writeExport(string $export, string $outputFile): void
    {
        $dir = dirname($outputFile);

        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf(
                'Unable to create directory %s',
                $dir,
            ));
        }

        if (false === @file_put_contents($outputFile, $export)) {
            throw new RuntimeException(sprintf(
                'Unable to write config into file %s',
                $outputFile,
            ));
        }

        if (false === @chmod($outputFile, 0666)) {
            throw new RuntimeException(sprintf(
                'Unable to chmod config file %s with mode 0666.',
                $outputFile,
            ));
        }
    }
}
