<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectoryFingerprint;
use function array_key_exists;
use function array_merge;
use function basename;
use function chmod;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function is_array;
use function is_dir;
use function is_readable;
use function mkdir;
use function rename;
use function serialize;
use function tempnam;
use function unlink;
use function unserialize;

/**
 * Persists command results across requests. The cache key is a fingerprint of the .git directory (HEAD commit
 * and the state of tags), so a commit, checkout, fetch or a new tag invalidates everything at once and no git
 * process is started for a repository whose state has not changed. When the fingerprint cannot be computed
 * (no .git directory, e.g. a production build reading an export file) the decorator is transparent.
 */
final class FileCachedGitRepository implements GitRepositoryInterface
{
    private const DEFAULT_ALLOWED_CLASSES = [
        Head::class,
        Tag::class,
        NearestTag::class,
        CommitHash::class,
    ];

    private GitRepositoryInterface $inner;

    private GitDirectoryFingerprint $fingerprint;

    private string $directory;

    /** @var array<int, class-string> */
    private array $allowedClasses;

    /** @var array<string, array<string, mixed>> fingerprint => command id => result */
    private array $loaded = [];

    /**
     * @param array<int, class-string> $allowedClasses Result classes accepted when reading the cache; extend it when caching custom commands
     */
    public function __construct(GitRepositoryInterface $inner, GitDirectoryFingerprint $fingerprint, string $directory, array $allowedClasses = [])
    {
        $this->inner = $inner;
        $this->fingerprint = $fingerprint;
        $this->directory = $directory;
        $this->allowedClasses = array_merge(self::DEFAULT_ALLOWED_CLASSES, $allowedClasses);
    }

    public function getSource(): string
    {
        return $this->inner->getSource();
    }

    public function isAccessible(): bool
    {
        return $this->inner->isAccessible();
    }

    public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
    {
        $this->inner->addHandler($commandClassname, $handler);
    }

    public function handle(GitCommandInterface $command)
    {
        $fingerprint = $this->fingerprint->compute();

        if (null === $fingerprint) {
            return $this->inner->handle($command);
        }

        $commandId = (string) $command;
        $entries = $this->load($fingerprint);

        if (array_key_exists($commandId, $entries)) {
            return $entries[$commandId];
        }

        $result = $this->inner->handle($command);
        $entries[$commandId] = $result;
        $this->store($fingerprint, $entries);

        return $result;
    }

    public function supports(string $commandClassname): bool
    {
        return $this->inner->supports($commandClassname);
    }

    /**
     * @return array<string, mixed>
     */
    private function load(string $fingerprint): array
    {
        if (isset($this->loaded[$fingerprint])) {
            return $this->loaded[$fingerprint];
        }

        $filename = $this->filename($fingerprint);
        $entries = [];

        if (is_readable($filename) && false !== ($content = @file_get_contents($filename))) {
            $decoded = @unserialize($content, ['allowed_classes' => $this->allowedClasses]);
            $entries = is_array($decoded) ? $decoded : [];
        }

        return $this->loaded[$fingerprint] = $entries;
    }

    /**
     * @param array<string, mixed> $entries
     */
    private function store(string $fingerprint, array $entries): void
    {
        $this->loaded[$fingerprint] = $entries;

        if (!is_dir($this->directory) && !@mkdir($this->directory, 0777, true) && !is_dir($this->directory)) {
            return;
        }

        # the state moved on, entries of previous fingerprints can never be hit again
        foreach (glob($this->directory . DIRECTORY_SEPARATOR . '*.cache') ?: [] as $stale) {
            if (basename($stale) !== $fingerprint . '.cache') {
                @unlink($stale);
            }
        }

        # atomic replace so a concurrent request never reads a half-written file
        $temporary = tempnam($this->directory, 'tgv');

        if (false === $temporary || false === @file_put_contents($temporary, serialize($entries))) {
            return;
        }

        # tempnam() creates the file with 0600; CLI and web server usually run as different users and both read the cache
        @chmod($temporary, 0666);

        if (!@rename($temporary, $this->filename($fingerprint))) {
            @unlink($temporary);
        }
    }

    private function filename(string $fingerprint): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . $fingerprint . '.cache';
    }
}
