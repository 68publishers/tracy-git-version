<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy;

use Throwable;

final class Helpers
{
	private function __construct()
	{
	}

	/**
	 * @param string $templatePath
	 * @param array  $params
	 *
	 * @return string
	 * @throws \Throwable
	 */
	public static function renderTemplate(string $templatePath, array $params = []): string
	{
		ob_start(static function () {
		});

		try {
			extract($params, EXTR_OVERWRITE);

			/** @noinspection PhpIncludeInspection */
			require $templatePath;

			return ob_get_clean();
		} catch (Throwable $e) {
			ob_end_clean();

			throw $e;
		}
	}
}
