<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SplFileInfo;
use function assert;
use function file_get_contents;
use function implode;
use function is_dir;
use function is_readable;
use function md5;
use function strlen;
use function strpos;
use function substr;
use function trim;

/**
 * Cheap identity of the repository state that command results depend on: the HEAD commit and the state of tags.
 * Built purely from file contents (HEAD, the branch ref, loose tag refs, packed-refs), so it does not depend on
 * timestamps and never runs git. NULL means the state cannot be determined and
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

        # tags: content of every loose ref under refs/tags and of packed-refs; content, not mtime, so a tag re-pointed
        # within the same second (`git tag -f`) or a re-packed file of the same size still changes the fingerprint
        $tagsDirectory = $gitDirectory . DIRECTORY_SEPARATOR . 'refs' . DIRECTORY_SEPARATOR . 'tags';

        if (is_dir($tagsDirectory)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tagsDirectory, FilesystemIterator::SKIP_DOTS));

            foreach ($iterator as $file) {
                assert($file instanceof SplFileInfo);
                $parts[] = $file->getPathname() . '=' . ($this->read($file->getPathname()) ?? '');
            }
        }

        $parts[] = $this->read($gitDirectory . DIRECTORY_SEPARATOR . 'packed-refs') ?? '';

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
