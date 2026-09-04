<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use function escapeshellarg;
use function preg_match;

/**
 * Resolving the nearest ancestor tag requires walking the commit graph, which is only feasible through the git binary.
 * Without the binary the handler returns NULL so a consumer can fall back to GetHeadCommand.
 *
 * `git describe` runs without a candidate limit so the result is exact even with many reachable tags; in a repository
 * with thousands of tags the first call after a change may take hundreds of milliseconds (FileCachedGitRepository
 * keeps the result until HEAD or the tags change).
 */
final class GetNearestTagCommandHandler extends AbstractLocalDirectoryCommandHandler
{
    private const UNLIMITED_CANDIDATES = 2147483647;

    private bool $useBinary;

    public function __construct(?GitDirectory $gitDirectory = null, bool $useBinary = false)
    {
        parent::__construct($gitDirectory);

        $this->useBinary = $useBinary;
    }

    /**
     * @throws GitDirectoryException
     */
    public function __invoke(GetNearestTagCommand $command): ?NearestTag
    {
        if (!$this->useBinary) {
            return null;
        }

        # git stops after 10 candidate tags by default and may then miss the nearest one in a history with many tags
        $describeOutput = $this->getGitDirectory()->executeGitCommand([
            'describe',
            '--tags',
            '--long',
            '--abbrev=40',
            '--candidates=' . self::UNLIMITED_CANDIDATES,
            'HEAD',
        ]);

        # `<tag>-<distance>-g<head commit>`; the tag name itself may contain dashes, so anchor on the two trailing parts
        if (0 !== $describeOutput['code'] || !preg_match('~^(?<tag>.+)-(?<distance>\d+)-g[0-9a-f]+$~', $describeOutput['out'], $matches)) {
            return null;
        }

        # rev-list resolves an annotated tag to the tagged commit, show-ref would return the tag object instead.
        # The ref is fully qualified so a tag named e.g. `HEAD` or like a branch is not resolved as that revision, and
        # quoted because the command runs through a shell and git allows `; | & $ ( )` in ref names.
        $commitOutput = $this->getGitDirectory()->executeGitCommand([
            'rev-list',
            '-n',
            '1',
            escapeshellarg('refs/tags/' . $matches['tag']),
            '--',
        ]);

        if (0 !== $commitOutput['code'] || '' === $commitOutput['out']) {
            return null;
        }

        return new NearestTag(
            new Tag($matches['tag'], new CommitHash($commitOutput['out'])),
            (int) $matches['distance'],
        );
    }
}
