<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console;

use SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console\Command\ExportRepositoryCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use function method_exists;

final class Application extends BaseApplication
{
    private const VERSION = '2.0';

    public function __construct()
    {
        parent::__construct('Tracy git version panel', self::VERSION);

        # Application::add() was removed in symfony/console 8.0, addCommand() exists since 7.4
        method_exists($this, 'addCommand') ? $this->addCommand(new ExportRepositoryCommand()) : $this->add(new ExportRepositoryCommand());
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new ListCommand()];
    }
}
