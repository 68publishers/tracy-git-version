<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Bridge\Nette\DI;

use RuntimeException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\ExportedGitRepository;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Export\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\Export\CommandHandler\GetLatestTagCommandHandler;

final class TracyGitVersionPanelExportExtension extends CompilerExtension
{
	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		$tempDir = $this->getContainerBuilder()->parameters['tempDir'] ?? NULL;

		return Expect::structure([
			'source_name' => Expect::string(GitRepositoryInterface::SOURCE_EXPORT),
			'export_filename' => Expect::string($tempDir ? $tempDir . '/git-version/repository.json' : NULL)->required(NULL === $tempDir),
			'command_handlers' => Expect::arrayOf(Expect::anyOf(Expect::type(Statement::class), Expect::string()), 'string')
				->default([
					GetHeadCommand::class => new Statement(GetHeadCommandHandler::class),
					GetLatestTagCommand::class => new Statement(GetLatestTagCommandHandler::class),
				])
				->mergeDefaults(TRUE)
				->before(static function (array $items) {
					return array_map(static function ($item) {
						return $item instanceof Statement ? $item : new Statement($item);
					}, $items);
				}),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		if (0 >= count($this->compiler->getExtensions(TracyGitVersionPanelExtension::class))) {
			throw new RuntimeException(sprintf(
				'The extension %s can be used only with %s.',
				static::class,
				TracyGitVersionPanelExtension::class
			));
		}

		if (!$this->isDebugMode()) {
			return;
		}

		$builder = $this->getContainerBuilder();

		# exported git repository
		$builder->addDefinition($this->prefix('git_repository.exported'))
			->setAutowired(FALSE)
			->setFactory(ExportedGitRepository::class, [
				'file' => $this->config->export_filename,
				'handlers' => $this->config->command_handlers,
				'source' => $this->config->source_name,
			])
			->addTag(TracyGitVersionPanelExtension::TAG_GIT_REPOSITORY, 50);
	}

	/**
	 * @return bool
	 */
	private function isDebugMode(): bool
	{
		return $this->getContainerBuilder()->parameters['debugMode'] ?? FALSE;
	}
}
