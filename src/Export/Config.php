<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export;

use SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\HeadExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\LatestTagExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;
use function array_merge;
use function array_key_exists;

final class Config
{
	public const OPTION_GIT_DIRECTORY = 'git_directory';
	public const OPTION_COMMAND_HANDLERS = 'command_handlers';
	public const OPTION_EXPORTERS = 'exporters';
	public const OPTION_OUTPUT_FILE = 'output_file';

	/** @var array<string, mixed> */
	private array $options = [];

	public static function create(): self
	{
		return new self();
	}

	public static function createDefault(): self
	{
		return self::create()
			->setGitDirectory(GitDirectory::createAutoDetected())
			->addCommandHandlers([
				GetHeadCommand::class => new GetHeadCommandHandler(),
				GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
			])
			->addExporters([
				new HeadExporter(),
				new LatestTagExporter(),
			]);
	}

	/**
	 * @param mixed $value
	 */
	public function setOption(string $name, $value): self
	{
		$this->options[$name] = $value;

		return $this;
	}

	/**
	 * @param mixed $value
	 */
	public function mergeOption(string $name, $value): self
	{
		$this->options[$name] = array_merge((array) ($this->options[$name] ?? []), (array) $value);

		return $this;
	}

	public function hasOption(string $name): bool
	{
		return array_key_exists($name, $this->options);
	}

	/**
	 * @return mixed
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	public function getOption(string $name)
	{
		if (!$this->hasOption($name)) {
			throw ExportConfigException::missingOption($name);
		}

		return $this->options[$name];
	}

	public function setGitDirectory(GitDirectory $gitDirectory): self
	{
		return $this->setOption(self::OPTION_GIT_DIRECTORY, $gitDirectory);
	}

	/**
	 * @param array<GitCommandHandlerInterface> $handlers
	 */
	public function addCommandHandlers(array $handlers): self
	{
		return $this->mergeOption(self::OPTION_COMMAND_HANDLERS, $handlers);
	}

	/**
	 * @param array<ExporterInterface> $exporters
	 *
	 * @return $this
	 */
	public function addExporters(array $exporters): self
	{
		return $this->mergeOption(self::OPTION_EXPORTERS, $exporters);
	}

	public function setOutputFile(string $outputFile): self
	{
		return $this->setOption(self::OPTION_OUTPUT_FILE, $outputFile);
	}
}
