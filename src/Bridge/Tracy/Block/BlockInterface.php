<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use Throwable;

interface BlockInterface
{
    /**
     * @throws Throwable
     */
    public function render(GitRepositoryInterface $gitRepository): string;
}
