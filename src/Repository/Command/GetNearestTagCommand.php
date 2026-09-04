<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Command;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;

/**
 * The most recent tag that is an ancestor of HEAD, together with the number of commits HEAD is ahead of it.
 * Unlike GetLatestTagCommand it answers "which version is this checkout based on", not "which tag is the newest in the repository".
 */
final class GetNearestTagCommand implements GitCommandInterface
{
    public function __toString(): string
    {
        return 'GET_NEAREST_TAG()';
    }
}
