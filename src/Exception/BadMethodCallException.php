<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Exception;

use SixtyEightPublishers\TracyGitVersionPanel\Export\ExporterInterface;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\ResolvableGitRepository;

final class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
	/**
	 * @param \SixtyEightPublishers\TracyGitVersionPanel\Export\ExporterInterface $exporter
	 *
	 * @return static
	 */
	public static function gitRepositoryNotProvidedForPartialExporter(ExporterInterface $exporter): self
	{
		return new self(sprintf(
			'Git repository for a partial exporter %s must be provided.',
			get_class($exporter)
		));
	}

	/**
	 * @param string $commandClassname
	 * @param string $handlerClassname
	 *
	 * @return static
	 */
	public static function cantAddHandlerToResolvableGitRepository(string $commandClassname, string $handlerClassname): self
	{
		return new self(sprintf(
			'Can\'t add new handler %s for the command %s into Git repository of type %s.',
			$commandClassname,
			$handlerClassname,
			ResolvableGitRepository::class
		));
	}
}
