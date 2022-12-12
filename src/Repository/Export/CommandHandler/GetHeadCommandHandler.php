<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;

final class GetHeadCommandHandler extends AbstractExportedCommandHandler
{
	public function __invoke(GetHeadCommand $command): Head
	{
		$value = $this->getExportedValue();

		return new Head(
			$value['head']['branch'] ?? null,
			isset($value['head']['commit_hash']) ? new CommitHash($value['head']['commit_hash']) : null
		);
	}
}
