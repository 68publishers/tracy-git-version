<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetHeadCommand;

final class GetHeadCommandHandler extends AbstractExportedCommandHandler
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetHeadCommand $command
	 *
	 * @return \SixtyEightPublishers\TracyGitVersionPanel\Repository\Entity\Head
	 */
	public function __invoke(GetHeadCommand $command): Head
	{
		$value = $this->getExportedValue();

		return new Head(
			$value['head']['branch'] ?? NULL,
			isset($value['head']['commit_hash']) ? new CommitHash($value['head']['commit_hash']) : NULL
		);
	}
}
