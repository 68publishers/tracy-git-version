<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export;

use SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

interface ExporterInterface
{
    /**
     * @return array<mixed, mixed>
     * @throws ExportConfigException
     */
    public function export(Config $config, ?GitRepositoryInterface $gitRepository): array;
}
