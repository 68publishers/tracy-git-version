<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersion\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

final class NearestTagExporter implements ExporterInterface
{
    /**
     * @throws BadMethodCallException
     * @throws UnhandledCommandException
     */
    public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
    {
        if (null === $gitRepository) {
            throw BadMethodCallException::gitRepositoryNotProvidedForPartialExporter($this);
        }

        if (!$gitRepository->supports(GetNearestTagCommand::class)) {
            return [];
        }

        $nearestTag = $gitRepository->handle(new GetNearestTagCommand());

        if (!$nearestTag instanceof NearestTag) {
            return [];
        }

        return [
            'nearest_tag' => [
                'name' => $nearestTag->getTag()->getName(),
                'commit_hash' => $nearestTag->getTag()->getCommitHash()->getValue(),
                'distance' => $nearestTag->getDistance(),
            ],
        ];
    }
}
