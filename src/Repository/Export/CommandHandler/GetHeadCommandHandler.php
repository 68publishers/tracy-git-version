<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;

final class GetHeadCommandHandler extends AbstractExportedCommandHandler
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand $command
	 *
	 * @return \SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head
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
