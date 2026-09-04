<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use __PHP_Incomplete_Class;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\CommitHash;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\NearestTag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectoryFingerprint;
use function array_key_exists;
use function array_merge;
use function chmod;
use function file_get_contents;
use function file_put_contents;
use function fileowner;
use function fileperms;
use function function_exists;
use function glob;
use function is_array;
use function is_dir;
use function is_readable;
use function is_writable;
use function mkdir;
use function posix_geteuid;
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
 *
 * The cache assumes a command result is a function of the HEAD commit and the tags. A custom command that reads
 * anything else (working tree status, remotes, stashes) must not go through this decorator.
 *
 * The directory must be owned by the process user and not writable by others, files are written 0644 and read back
 * only when owned by the process user; otherwise the cache is skipped, so on a shared machine another local user cannot
 * plant or swap a crafted result. NULL results are not cached because NULL also stands for "git is unavailable".
 * Use a directory dedicated to the application (e.g. its temp directory): pruning removes the other `tgv-*.cache`
 * files there, two applications sharing one directory would evict each other.
 */
final class FileCachedGitRepository implements GitRepositoryInterface
{
    private const FILE_PREFIX = 'tgv-';

    private const FILE_SUFFIX = '.cache';

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

        # NULL also means "git is not available right now" (no binary, failed process); such an answer must not outlive the outage
        if (null === $result) {
            return null;
        }

        # the repository may have moved on while git was running; a result computed for the new state must not be filed under the old one
        if ($fingerprint !== $this->fingerprint->compute()) {
            return $result;
        }

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

        # a file another local user could have written or swapped is never deserialized
        if ($this->isSafeDirectory() && $this->isOwnFile($filename) && false !== ($content = @file_get_contents($filename))) {
            $decoded = @unserialize($content, ['allowed_classes' => $this->allowedClasses]);
            $entries = is_array($decoded) ? $decoded : [];

            # a result class missing from $allowedClasses comes back as an incomplete object: treat it as a miss, not as a value
            foreach ($entries as $commandId => $entry) {
                if ($entry instanceof __PHP_Incomplete_Class) {
                    unset($entries[$commandId]);
                }
            }
        }

        return $this->loaded[$fingerprint] = $entries;
    }

    /**
     * @param array<string, mixed> $entries
     */
    private function store(string $fingerprint, array $entries): void
    {
        $this->loaded[$fingerprint] = $entries;

        if (!is_dir($this->directory) && !@mkdir($this->directory, 0755, true) && !is_dir($this->directory)) {
            return;
        }

        # a directory that exists but is not writable would make tempnam() fall back to the system directory with a notice;
        # a directory other users can write to is refused as well, they could swap the file between the ownership check and the read
        if (!is_writable($this->directory) || !$this->isSafeDirectory()) {
            return;
        }

        # the state moved on, entries of previous fingerprints can never be hit again; only own files are touched
        foreach (glob($this->directory . DIRECTORY_SEPARATOR . self::FILE_PREFIX . '*' . self::FILE_SUFFIX) ?: [] as $stale) {
            if ($stale !== $this->filename($fingerprint)) {
                @unlink($stale);
            }
        }

        # atomic replace so a concurrent request never reads a half-written file
        $temporary = @tempnam($this->directory, 'tgv');

        if (false === $temporary || false === @file_put_contents($temporary, serialize($entries))) {
            return;
        }

        # readable for everyone, writable only by the owner: a cache file another user could modify is never trusted (see isOwnFile)
        @chmod($temporary, 0644);

        if (!@rename($temporary, $this->filename($fingerprint))) {
            @unlink($temporary);
        }
    }

    private function isOwnFile(string $filename): bool
    {
        if (!is_readable($filename)) {
            return false;
        }

        return $this->isOwnedByProcess($filename);
    }

    /**
     * Owned by the process and not writable by group or others. Checked before every read and write.
     */
    private function isSafeDirectory(): bool
    {
        $permissions = @fileperms($this->directory);

        if (false === $permissions || 0 !== ($permissions & 0022)) {
            return false;
        }

        return $this->isOwnedByProcess($this->directory);
    }

    private function isOwnedByProcess(string $path): bool
    {
        # getmyuid() would return the owner of the script file, the process may run as someone else
        if (!function_exists('posix_geteuid')) {
            return true;
        }

        $owner = @fileowner($path);

        return false === $owner || $owner === posix_geteuid();
    }

    private function filename(string $fingerprint): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . self::FILE_PREFIX . $fingerprint . self::FILE_SUFFIX;
    }
}
