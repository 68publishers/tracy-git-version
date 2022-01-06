<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersionPanel\Export\Config;
use SixtyEightPublishers\TracyGitVersionPanel\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface;

final class HeadExporter implements ExporterInterface
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

		if (!$gitRepository->supports(GetHeadCommand::class)) {
			return [];
		}

		/** @var \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Head $head */
		$head = $gitRepository->handle(new GetHeadCommand());

		return [
			'head' => [
				'branch' => $head->getBranch(),
				'commit_hash' => NULL !== $head->getCommitHash() ? $head->getCommitHash()->getValue() : NULL,
			],
		];
	}
}
