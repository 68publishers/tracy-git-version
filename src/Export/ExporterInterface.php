<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export;

use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

interface ExporterInterface
{
	/**
	 * @return array<mixed, mixed>
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	public function export(Config $config, ?GitRepositoryInterface $gitRepository): array;
}
