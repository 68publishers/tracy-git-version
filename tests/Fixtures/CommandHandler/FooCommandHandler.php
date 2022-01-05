<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersionPanel\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\CommandHandler\AbstractLocalDirectoryCommandHandler;

final class FooCommandHandler extends AbstractLocalDirectoryCommandHandler
{
	public int $callingCounter = 0;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Tests\Fixtures\Command\FooCommand $command
	 *
	 * @return string
	 */
	public function __invoke(FooCommand $command): string
	{
		$this->callingCounter++;

		return $this->getGitDirectoryPath() . '/foo';
	}

	/**
	 * @return string
	 */
	protected function getGitDirectoryPath(): string
	{
		try {
			return (string) $this->getGitDirectory();
		} catch (GitDirectoryException $e) {
			return 'undefined';
		}
	}
}
