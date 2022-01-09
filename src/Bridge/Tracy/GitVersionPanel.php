<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy;

use Tracy\IBarPanel;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\BlockInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\CurrentStateBlock;
use SixtyEightPublishers\TracyGitVersion\Repository\RuntimeCachedGitRepository;

final class GitVersionPanel implements IBarPanel
{
	private GitRepositoryInterface $gitRepository;

	/** @var \SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\BlockInterface[]  */
	private array $blocks;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface   $gitRepository
	 * @param \SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\BlockInterface[] $blocks
	 */
	public function __construct(GitRepositoryInterface $gitRepository, array $blocks)
	{
		$this->gitRepository = $gitRepository;
		$this->blocks = (static fn (BlockInterface ...$blocks): array => $blocks)(...$blocks);
	}

	/**
	 * @param string|NULL $workingDirectory
	 * @param string      $directoryName
	 * @param array       $handlers
	 *
	 * @return static
	 */
	public static function createDefault(?string $workingDirectory = NULL, string $directoryName = '.git', array $handlers = []): self
	{
		$repository = new RuntimeCachedGitRepository(LocalGitRepository::createDefault($workingDirectory, $directoryName));

		foreach ($handlers as $command => $handler) {
			$repository->addHandler($command, $handler);
		}

		return new self($repository, [
			new CurrentStateBlock(),
		]);
	}

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block\BlockInterface $block
	 *
	 * @return $this
	 */
	public function addBlock(BlockInterface $block): self
	{
		$this->blocks[] = $block;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function getTab(): string
	{
		return Helpers::renderTemplate(__DIR__ . '/templates/GitVersionPanel.tab.phtml', [
			'gitRepository' => $this->gitRepository,
		]);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function getPanel(): string
	{
		return Helpers::renderTemplate(__DIR__ . '/templates/GitVersionPanel.panel.phtml', [
			'gitRepository' => $this->gitRepository,
			'blocks' => $this->blocks,
		]);
	}
}
