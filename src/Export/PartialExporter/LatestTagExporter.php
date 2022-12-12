<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersion\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;

final class LatestTagExporter implements ExporterInterface
{
	/**
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
	{
		if (null === $gitRepository) {
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
