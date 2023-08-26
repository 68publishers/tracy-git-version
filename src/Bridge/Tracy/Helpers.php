<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy;

use Throwable;
use function extract;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;

final class Helpers
{
    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $params
     *
     * @throws Throwable
     */
    public static function renderTemplate(string $templatePath, array $params = []): string
    {
        ob_start(static function () {
        });

        try {
            extract($params, EXTR_OVERWRITE);

            require $templatePath;

            return (string) ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }
    }
}
