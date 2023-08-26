<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;

final class GetLatestTagCommandHandler extends AbstractExportedCommandHandler
{
    public function __invoke(GetLatestTagCommand $command): ?Tag
    {
        $value = $this->getExportedValue();

        if (!isset($value['latest_tag']['name'], $value['latest_tag']['commit_hash'])) {
            return null;
        }

        return new Tag($value['latest_tag']['name'], new CommitHash($value['latest_tag']['commit_hash']));
    }
}
