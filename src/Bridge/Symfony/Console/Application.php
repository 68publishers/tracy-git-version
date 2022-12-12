<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console;

use PhpCsFixer\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Application as BaseApplication;
use SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console\Command\ExportRepositoryCommand;

final class Application extends BaseApplication
{
	private const VERSION = '1.0';

	public function __construct()
	{
		parent::__construct('Tracy git version panel', self::VERSION);

		$this->add(new ExportRepositoryCommand());
	}

	protected function getDefaultCommands(): array
	{
		return [new HelpCommand(), new ListCommand()];
	}
}
