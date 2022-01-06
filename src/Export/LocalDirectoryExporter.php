<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Export;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface;

final class LocalDirectoryExporter implements ExporterInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
	{
		$gitRepository = $gitRepository ?? new LocalGitRepository(
			$config->getOption(Config::OPTION_GIT_DIRECTORY),
			(array) $config->getOption(Config::OPTION_COMMAND_HANDLERS),
		);

		$results = [[]];
		$exporters = (array) $config->getOption(Config::OPTION_EXPORTERS);
		$exporters = (static fn (ExporterInterface ...$exporters): array => $exporters)(...$exporters);

		foreach ($exporters as $exporter) {
			$results[] = $exporter->export($config, $gitRepository);
		}

		return array_merge(...$results);
	}
}
