<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use function clearstatcache;
use function file_get_contents;
use function filemtime;
use function filesize;
use function implode;
use function is_readable;
use function md5;
use function scandir;
use function strlen;
use function strpos;
use function substr;
use function trim;

/**
 * Cheap identity of the repository state that command results depend on: the HEAD commit and the state of tags.
 * Reads a few files from the .git directory, never runs git. NULL means the state cannot be determined and
 * results must not be cached.
 */
final class GitDirectoryFingerprint
{
    private GitDirectory $gitDirectory;

    public function __construct(GitDirectory $gitDirectory)
    {
        $this->gitDirectory = $gitDirectory;
    }

    public function compute(): ?string
    {
        try {
            $gitDirectory = $this->gitDirectory->__toString();
        } catch (GitDirectoryException $e) {
            return null;
        }

        $head = $this->read($gitDirectory . DIRECTORY_SEPARATOR . 'HEAD');

        if (null === $head) {
            return null;
        }

        $parts = [$head];

        # symbolic HEAD: the branch tip lives in a loose ref file or, after `git pack-refs`, in packed-refs (covered by its mtime below)
        if (0 === strpos($head, 'ref:')) {
            $parts[] = $this->read($gitDirectory . DIRECTORY_SEPARATOR . trim(substr($head, 4, strlen($head)))) ?? 'packed';
        }

        # tags: loose refs are files in refs/tags (names + directory mtime), fetched or packed tags rewrite packed-refs
        $tagsDirectory = $gitDirectory . DIRECTORY_SEPARATOR . 'refs' . DIRECTORY_SEPARATOR . 'tags';
        $packedRefs = $gitDirectory . DIRECTORY_SEPARATOR . 'packed-refs';

        clearstatcache(true, $tagsDirectory);
        clearstatcache(true, $packedRefs);

        $parts[] = implode(',', @scandir($tagsDirectory) ?: []);
        $parts[] = (string) @filemtime($tagsDirectory);
        $parts[] = (string) @filemtime($packedRefs);
        $parts[] = (string) @filesize($packedRefs);

        return md5(implode('|', $parts));
    }

    private function read(string $filename): ?string
    {
        if (!is_readable($filename)) {
            return null;
        }

        $content = @file_get_contents($filename);

        return false === $content ? null : trim($content);
    }
}
