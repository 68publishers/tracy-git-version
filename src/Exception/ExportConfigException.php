<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Exception;

use Exception;
use SixtyEightPublishers\TracyGitVersionPanel\Export\Config;

final class ExportConfigException extends Exception implements ExceptionInterface
{
	/**
	 * @param string $name
	 *
	 * @return static
	 */
	public static function missingOption(string $name): self
	{
		return new self(sprintf(
			'Missing the option %s in export config.',
			$name
		));
	}

	/**
	 * @param string $filename
	 *
	 * @return static
	 */
	public static function configFileNotFound(string $filename): self
	{
		return new self(sprintf(
			'Config file %s not found.',
			$filename
		));
	}

	/**
	 * @param string $filename
	 *
	 * @return static
	 */
	public static function configCantBeLoadedFromFile(string $filename): self
	{
		return new self(sprintf(
			'Config from the file %s can\'t be loaded. The file must return instance of %s.',
			$filename,
			Config::class
		));
	}
}
