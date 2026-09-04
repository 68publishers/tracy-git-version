<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository\Entity;

final class NearestTag
{
    private Tag $tag;

    private int $distance;

    /**
     * @param int $distance Number of commits HEAD is ahead of the tag, 0 when HEAD points at the tagged commit.
     */
    public function __construct(Tag $tag, int $distance)
    {
        $this->tag = $tag;
        $this->distance = $distance;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function isExact(): bool
    {
        return 0 === $this->distance;
    }
}
