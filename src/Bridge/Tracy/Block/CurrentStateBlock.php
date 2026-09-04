<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use function assert;
use function sprintf;

final class CurrentStateBlock implements BlockInterface
{
    public function render(GitRepositoryInterface $gitRepository): string
    {
        $head = $gitRepository->supports(GetHeadCommand::class) ? $gitRepository->handle(new GetHeadCommand()) : new Head(null, null);
        $latestTag = $gitRepository->supports(GetLatestTagCommand::class) ? $gitRepository->handle(new GetLatestTagCommand()) : null;
        $nearestTag = $gitRepository->supports(GetNearestTagCommand::class) ? $gitRepository->handle(new GetNearestTagCommand()) : null;
        assert($head instanceof Head && (null === $latestTag || $latestTag instanceof Tag) && (null === $nearestTag || $nearestTag instanceof NearestTag));

        $isHeadOnLatestTag = $latestTag instanceof Tag && null !== $head->getCommitHash() && $head->getCommitHash()->compare($latestTag->getCommitHash());

        $block = new SimpleTableBlock([
            'Branch' => $head->getBranch() ?? ($head->isDetached() ? 'detached' : 'not versioned'),
            'Commit' => null !== $head->getCommitHash() ? $head->getCommitHash()->getValue() : 'not versioned',
            'Latest tag' => $latestTag instanceof Tag ? sprintf('%s (%s)', $latestTag->getName(), $isHeadOnLatestTag ? 'current commit' : 'last known') : 'unknown',
            'Nearest tag' => $nearestTag instanceof NearestTag ? sprintf('%s (%s)', $nearestTag->getTag()->getName(), $nearestTag->isExact() ? 'current commit' : sprintf(1 === $nearestTag->getDistance() ? '+%d commit' : '+%d commits', $nearestTag->getDistance())) : 'unknown',
        ], 'Current state');

        return $block->render($gitRepository);
    }
}
