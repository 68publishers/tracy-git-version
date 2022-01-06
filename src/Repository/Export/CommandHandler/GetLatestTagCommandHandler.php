<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand;

final class GetLatestTagCommandHandler extends AbstractExportedCommandHandler
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand $command
	 *
	 * @return \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Tag|NULL
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
