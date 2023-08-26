<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersion\Exception\BadMethodCallException;
use SixtyEightPublishers\TracyGitVersion\Exception\UnhandledCommandException;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use function assert;

final class HeadExporter implements ExporterInterface
{
    /**
     * @throws UnhandledCommandException
     */
    public function export(Config $config, ?GitRepositoryInterface $gitRepository): array
    {
        if (null === $gitRepository) {
            throw BadMethodCallException::gitRepositoryNotProvidedForPartialExporter($this);
        }

        if (!$gitRepository->supports(GetHeadCommand::class)) {
            return [];
        }

        $head = $gitRepository->handle(new GetHeadCommand());
        assert($head instanceof Head);

        return [
            'head' => [
                'branch' => $head->getBranch(),
                'commit_hash' => null !== $head->getCommitHash() ? $head->getCommitHash()->getValue() : null,
            ],
        ];
    }
}
