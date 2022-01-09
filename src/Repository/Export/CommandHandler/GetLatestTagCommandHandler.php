<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;

final class GetLatestTagCommandHandler extends AbstractExportedCommandHandler
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand $command
	 *
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag|NULL
	 */
	public function __invoke(GetLatestTagCommand $command): ?Tag
	{
		$value = $this->getExportedValue();

		if (!isset($value['latest_tag']['name'], $value['latest_tag']['commit_hash'])) {
			return NULL;
		}

		return new Tag($value['latest_tag']['name'], new CommitHash($value['latest_tag']['commit_hash']));
	}
}
