<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export;

use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use function assert;
use function array_merge;

final class LocalDirectoryExporter implements ExporterInterface
{
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
	{
		$directory = $config->getOption(Config::OPTION_GIT_DIRECTORY);
		assert($directory instanceof GitDirectory);

		/** @var array<class-string, GitCommandHandlerInterface> $commandHandlers */
		$commandHandlers = (array) $config->getOption(Config::OPTION_COMMAND_HANDLERS);

		$gitRepository = $gitRepository ?? new LocalGitRepository($directory, $commandHandlers);
		$results = [[]];

		/** @var array<ExporterInterface> $exporters */
		$exporters = (array) $config->getOption(Config::OPTION_EXPORTERS);
		$exporters = (static fn (ExporterInterface ...$exporters): array => $exporters)(...$exporters);

		foreach ($exporters as $exporter) {
			$results[] = $exporter->export($config, $gitRepository);
		}

		return array_merge(...$results);
	}
}
