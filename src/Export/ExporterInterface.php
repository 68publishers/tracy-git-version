<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Export;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface;

interface ExporterInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Export\Config                          $config
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface|null $gitRepository
	 *
	 * @return array
	 * @throws \SixtyEightPublishers\TracyGitVersionPanel\Exception\ExportConfigException
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array;
}
