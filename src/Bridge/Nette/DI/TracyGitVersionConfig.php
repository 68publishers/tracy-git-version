<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI;

use Nette\DI\Definitions\Statement;

final class TracyGitVersionConfig
{
    public string $source_name;

    /** @var array<class-string, Statement> */
    public array $command_handlers;

    public TracyGitVersionPanelConfig $panel;
}
