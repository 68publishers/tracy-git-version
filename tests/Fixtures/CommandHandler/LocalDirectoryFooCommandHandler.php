<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\CommandHandler;

use SixtyEightPublishers\TracyGitVersion\Exception\GitDirectoryException;
use SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\AbstractLocalDirectoryCommandHandler;

final class LocalDirectoryFooCommandHandler extends AbstractLocalDirectoryCommandHandler
{
	public int $callingCounter = 0;

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command\FooCommand $command
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
