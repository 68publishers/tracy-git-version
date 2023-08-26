<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use RuntimeException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetLatestTagCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\ExportedGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use function array_map;
use function assert;
use function count;
use function sprintf;

final class TracyGitVersionExportExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        $tempDir = $this->getContainerBuilder()->parameters['tempDir'] ?? null;

        return Expect::structure([
            'source_name' => Expect::string(GitRepositoryInterface::SOURCE_EXPORT),
            'export_filename' => Expect::string($tempDir ? $tempDir . '/git-version/repository.json' : null)->required(null === $tempDir),
            'command_handlers' => Expect::arrayOf(Expect::anyOf(Expect::type(Statement::class), Expect::string()), 'string')
                ->default([
                    GetHeadCommand::class => new Statement(GetHeadCommandHandler::class),
                    GetLatestTagCommand::class => new Statement(GetLatestTagCommandHandler::class),
                ])
                ->mergeDefaults()
                ->before(static function (array $items) {
                    return array_map(static function ($item) {
                        return $item instanceof Statement ? $item : new Statement($item);
                    }, $items);
                }),
        ])->castTo(TracyGitVersionExportConfig::class);
    }

    public function loadConfiguration(): void
    {
        if (0 >= count($this->compiler->getExtensions(TracyGitVersionExtension::class))) {
            throw new RuntimeException(sprintf(
                'The extension %s can be used only with %s.',
                self::class,
                TracyGitVersionExtension::class,
            ));
        }

        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();
        assert($config instanceof TracyGitVersionExportConfig);

        # exported git repository
        $builder->addDefinition($this->prefix('git_repository.exported'))
            ->setAutowired(false)
            ->setFactory(ExportedGitRepository::class, [
                'file' => $config->export_filename,
                'handlers' => $config->command_handlers,
                'source' => $config->source_name,
            ])
            ->addTag(TracyGitVersionExtension::TAG_GIT_REPOSITORY, 50);
    }
}
