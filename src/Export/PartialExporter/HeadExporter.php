<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersion\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class HeadExporter implements ExporterInterface
{
	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
	{
		if (NULL === $gitRepository) {
			throw BadMethodCallException::gitRepositoryNotProvidedForPartialExporter($this);
		}

		if (!$gitRepository->supports(GetHeadCommand::class)) {
			return [];
		}

		/** @var \SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head $head */
		$head = $gitRepository->handle(new GetHeadCommand());

		return [
			'head' => [
				'branch' => $head->getBranch(),
				'commit_hash' => NULL !== $head->getCommitHash() ? $head->getCommitHash()->getValue() : NULL,
			],
		];
	}
}
