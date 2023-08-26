<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy;

use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\BlockInterface;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\CurrentStateBlock;
use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Repository\RuntimeCachedGitRepository;
use Throwable;
use Tracy\IBarPanel;

final class GitVersionPanel implements IBarPanel
{
    private GitRepositoryInterface $gitRepository;

    /** @var array<BlockInterface> */
    private array $blocks;

    /**
     * @param array<BlockInterface> $blocks
     */
    public function __construct(GitRepositoryInterface $gitRepository, array $blocks)
    {
        $this->gitRepository = $gitRepository;
        $this->blocks = (static fn (BlockInterface ...$blocks): array => $blocks)(...$blocks);
    }

    /**
     * @param array<class-string, GitCommandHandlerInterface> $handlers
     */
    public static function createDefault(?string $workingDirectory = null, string $directoryName = '.git', array $handlers = []): self
    {
        $repository = new RuntimeCachedGitRepository(LocalGitRepository::createDefault($workingDirectory, $directoryName));

        foreach ($handlers as $command => $handler) {
            $repository->addHandler($command, $handler);
        }

        return new self($repository, [
            new CurrentStateBlock(),
        ]);
    }

    public function addBlock(BlockInterface $block): self
    {
        $this->blocks[] = $block;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getTab(): string
    {
        return Helpers::renderTemplate(__DIR__ . '/templates/GitVersionPanel.tab.phtml', [
            'gitRepository' => $this->gitRepository,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function getPanel(): string
    {
        return Helpers::renderTemplate(__DIR__ . '/templates/GitVersionPanel.panel.phtml', [
            'gitRepository' => $this->gitRepository,
            'blocks' => $this->blocks,
        ]);
    }
}
