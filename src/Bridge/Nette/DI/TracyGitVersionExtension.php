<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI;

use Tracy\Bar;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\Definitions\ServiceDefinition;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\GitVersionPanel;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\ResolvableGitRepository;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\CurrentStateBlock;
use SixtyEightPublishers\TracyGitVersion\Repository\RuntimeCachedGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;

final class TracyGitVersionExtension extends CompilerExtension
{
	public const TAG_GIT_REPOSITORY = '68publishers.tracy_git_version_panel.tag.git_repository';

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'source_name' => Expect::string(GitRepositoryInterface::SOURCE_GIT_DIRECTORY),
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
			'panel' => Expect::structure([
				'blocks' => Expect::listOf(Expect::anyOf(Expect::type(Statement::class), Expect::string()))
					->default([
						new Statement(CurrentStateBlock::class),
					])
					->mergeDefaults(TRUE)
					->before(static function (array $items) {
						return array_map(static function ($item) {
							return $item instanceof Statement ? $item : new Statement($item);
						}, $items);
					}),
			]),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		# default git repository service
		$builder->addDefinition($this->prefix('git_repository'))
			->setAutowired(GitRepositoryInterface::class)
			->setType(GitRepositoryInterface::class)
			->setFactory($this->prefix('@git_repository.runtime_cached'));

		# runtime cached git repository
		$builder->addDefinition($this->prefix('git_repository.runtime_cached'))
			->setAutowired(FALSE)
			->setFactory(RuntimeCachedGitRepository::class, [$this->prefix('@git_repository.resolvable')]);

		# resolvable git repository
		$builder->addDefinition($this->prefix('git_repository.resolvable'))
			->setAutowired(FALSE)
			->setType(ResolvableGitRepository::class);

		# local directory git repository
		$builder->addDefinition($this->prefix('git_repository.local_directory'))
			->setAutowired(FALSE)
			->setFactory(LocalGitRepository::class, [
				'gitDirectory' => new Statement([GitDirectory::class, 'createAutoDetected']),
				'handlers' => $this->config->command_handlers,
				'source' => $this->config->source_name,
			])
			->addTag(self::TAG_GIT_REPOSITORY, 100);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		# pass git repositories into resolvable repository
		$resolvableGitRepository = $builder->getDefinition($this->prefix('git_repository.resolvable'));
		assert($resolvableGitRepository instanceof ServiceDefinition);

		$repositories = array_map('intval', $builder->findByTag(self::TAG_GIT_REPOSITORY));

		arsort($repositories);

		$resolvableGitRepository->setArguments([
			array_map(static fn (string $serviceName): string => '@' . $serviceName, array_keys($repositories)),
		]);

		# add panel into Tracy
		if (!$this->isDebugMode()) {
			return;
		}

		$barServiceName = $builder->getByType(Bar::class, FALSE);

		if (NULL === $barServiceName) {
			return;
		}

		$barService = $builder->getDefinition($barServiceName);
		assert($barService instanceof ServiceDefinition);

		$barService->addSetup('addPanel', [
			new Statement(GitVersionPanel::class, [
				$this->prefix('@git_repository'),
				$this->config->panel->blocks,
			]),
		]);
	}

	/**
	 * @return bool
	 */
	private function isDebugMode(): bool
	{
		return $this->getContainerBuilder()->parameters['debugMode'] ?? FALSE;
	}
}
