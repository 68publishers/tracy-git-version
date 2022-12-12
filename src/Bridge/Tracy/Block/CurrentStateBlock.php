<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use function assert;
use function sprintf;

final class CurrentStateBlock implements BlockInterface
{
	public function render(GitRepositoryInterface $gitRepository): string
	{
		$head = $gitRepository->supports(GetHeadCommand::class) ? $gitRepository->handle(new GetHeadCommand()) : new Head(null, null);
		$latestTag = $gitRepository->supports(GetLatestTagCommand::class) ? $gitRepository->handle(new GetLatestTagCommand()) : null;
		assert($head instanceof Head && (null === $latestTag || $latestTag instanceof Tag));

		$isHeadOnLatestTag = $latestTag instanceof Tag && null !== $head->getCommitHash() && $head->getCommitHash()->compare($latestTag->getCommitHash());

		$block = new SimpleTableBlock([
			'Branch' => $head->getBranch() ?? ($head->isDetached() ? 'detached' : 'not versioned'),
			'Commit' => null !== $head->getCommitHash() ? $head->getCommitHash()->getValue() : 'not versioned',
			'Latest tag' => $latestTag instanceof Tag ? sprintf('%s (%s)', $latestTag->getName(), $isHeadOnLatestTag ? 'current commit' : 'last known') : 'unknown',
		], 'Current state');

		return $block->render($gitRepository);
	}
}
