<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export;

use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

interface ExporterInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Export\Config                          $config
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface|null $gitRepository
	 *
	 * @return array
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array;
}
