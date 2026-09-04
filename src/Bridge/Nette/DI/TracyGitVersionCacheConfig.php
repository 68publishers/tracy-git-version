<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI;

final class TracyGitVersionCacheConfig
{
    public bool $enabled;

    public ?string $directory;
}
