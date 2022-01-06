<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersionPanel\Export\Config;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersionPanel\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand;

final class LatestTagExporter implements ExporterInterface
{
	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\TracyGitVersionPanel\Exception\UnhandledCommandException
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
	{
		if (NULL === $gitRepository) {
			throw BadMethodCallException::gitRepositoryNotProvidedForPartialExporter($this);
		}

		if (!$gitRepository->supports(GetLatestTagCommand::class)) {
			return [];
		}

		$tag = $gitRepository->handle(new GetLatestTagCommand());

		if (!$tag instanceof Tag) {
			return [];
		}

		return [
			'latest_tag' => [
				'name' => $tag->getName(),
				'commit_hash' => $tag->getCommitHash()->getValue(),
			],
		];
	}
}
