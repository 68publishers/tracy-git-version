<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Bridge\Nette\DI;

use RuntimeException;
use SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI\TracyGitVersionExportExtension;
use SixtyEightPublishers\TracyGitVersion\Bridge\Nette\DI\TracyGitVersionExtension;
use Tester\Assert;
use Tester\TestCase;
use function sprintf;

require __DIR__ . '/../../../bootstrap.php';

final class TracyGitVersionExportExtensionTest extends TestCase
{
    public function testBasicIntegration(): void
    {
        Assert::noError(static fn () => ContainerFactory::create(__DIR__ . '/config.export.neon', true));
    }

    public function testBasicIntegrationWithoutMainExtension(): void
    {
        Assert::exception(
            static fn () => ContainerFactory::create(__DIR__ . '/config.export.error.missingMainExtension.neon', false),
            RuntimeException::class,
            sprintf('The extension %s can be used only with %s.', TracyGitVersionExportExtension::class, TracyGitVersionExtension::class),
        );
    }
}

(new TracyGitVersionExportExtensionTest())->run();
