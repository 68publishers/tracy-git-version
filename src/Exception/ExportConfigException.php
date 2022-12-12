<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Exception;

use Exception;
use SixtyEightPublishers\TracyGitVersion\Export\Config;
use function sprintf;

final class ExportConfigException extends Exception implements ExceptionInterface
{
	public static function missingOption(string $name): self
	{
		return new self(sprintf(
			'Missing the option %s in export config.',
			$name
		));
	}

	public static function configFileNotFound(string $filename): self
	{
		return new self(sprintf(
			'Config file %s not found.',
			$filename
		));
	}

	public static function configCantBeLoadedFromFile(string $filename): self
	{
		return new self(sprintf(
			'Config from the file %s can\'t be loaded. The file must return instance of %s.',
			$filename,
			Config::class
		));
	}
}
