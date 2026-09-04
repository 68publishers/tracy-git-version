<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;

final class GetNearestTagCommandHandler extends AbstractExportedCommandHandler
{
    public function __invoke(GetNearestTagCommand $command): ?NearestTag
    {
        $value = $this->getExportedValue();

        if (!isset($value['nearest_tag']['name'], $value['nearest_tag']['commit_hash'], $value['nearest_tag']['distance'])) {
            return null;
        }

        return new NearestTag(
            new Tag($value['nearest_tag']['name'], new CommitHash($value['nearest_tag']['commit_hash'])),
            (int) $value['nearest_tag']['distance'],
        );
    }
}
