<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use function explode;
use function file_get_contents;
use function is_readable;
use function strlen;
use function strpos;
use function substr;
use function trim;

final class GetHeadCommandHandler extends AbstractLocalDirectoryCommandHandler
{
    /**
     * @throws GitDirectoryException
     */
    public function __invoke(GetHeadCommand $command): Head
    {
        $headFile = $this->getGitDirectory() . DIRECTORY_SEPARATOR . 'HEAD';

        # not versioned
        if (!is_readable($headFile) || false === ($content = @file_get_contents($headFile))) {
            return new Head(null, null);
        }

        # detached head
        if (0 !== strpos($content, 'ref:')) {
            return new Head(null, new CommitHash(trim($content)));
        }

        $branchParts = explode('/', $content, 3);
        $commitFile = $this->getGitDirectory() . DIRECTORY_SEPARATOR . trim(substr($content, 5, strlen($content)));

        return new Head(
            isset($branchParts[2]) ? trim($branchParts[2]) : null,
            is_readable($commitFile) && false !== ($commitHash = @file_get_contents($commitFile)) ? new CommitHash(trim($commitHash)) : null,
        );
    }
}
