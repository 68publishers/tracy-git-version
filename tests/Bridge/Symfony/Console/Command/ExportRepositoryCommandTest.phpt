<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Bridge\Symfony\Console\Command;

use Exception;
use SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console\Application;
use SixtyEightPublishers\TracyGitVersion\Bridge\Symfony\Console\Command\ExportRepositoryCommand;
use SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use Tester\TestCase;
use function array_merge;
use function file_exists;
use function file_get_contents;
use function is_readable;
use function json_decode;
use function unlink;

require __DIR__ . '/../../../../bootstrap.php';

final class ExportRepositoryCommandTest extends TestCase
{
    public function testWithoutArguments(): void
    {
        Assert::exception(
            fn () => $this->doTestExecute([]),
            ExportConfigException::class,
            'Missing the option output_file in export config.',
        );
    }

    public function testWithNonExistentConfig(): void
    {
        Assert::exception(
            fn () => $this->doTestExecute([
                '--config' => __DIR__ . '/non-existent-config.php',
            ]),
            ExportConfigException::class,
            'Config file %a%/non-existent-config.php not found.',
        );
    }

    public function testWithCustomConfig(): void
    {
        $commandTester = $this->doTestExecute([
            '--config' => __DIR__ . '/../../../../files/export/config.php',
        ]);

        Assert::same(0, $commandTester->getStatusCode());

        $filename = __DIR__ . '/../../../../files/output/git-repository-export.json';

        $this->doTestExportedFile($filename);
        $this->removeFile($filename);
    }

    public function testWithCustomConfigAndOutputFileOption(): void
    {
        $filename = __DIR__ . '/../../../../files/output/git-repository-export-custom.json';
        $commandTester = $this->doTestExecute([
            '--config' => __DIR__ . '/../../../../files/export/config.php',
            '--output-file' => $filename,
        ]);

        Assert::same(0, $commandTester->getStatusCode());

        $this->doTestExportedFile($filename);
        $this->removeFile($filename);
    }

    public function testDumpOnly(): void
    {
        $filename = __DIR__ . '/../../../../files/output/non-existent-export.json';
        $commandTester = $this->doTestExecute([
            '--config' => __DIR__ . '/../../../../files/export/config.php',
            '--output-file' => $filename,
            '--dump-only' => true,
        ]);

        Assert::same(0, $commandTester->getStatusCode());
        Assert::false(file_exists($filename));
    }

    private function doTestExecute(array $arguments): CommandTester
    {
        $application = new Application();
        $application->add(new ExportRepositoryCommand());

        $command = $application->find('export-repository');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array_merge(['command' => $command->getName()], $arguments),
            [
                'interactive' => false,
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ],
        );

        return $commandTester;
    }

    /**
     * @throws Exception
     */
    private function doTestExportedFile(string $filename): void
    {
        Assert::true(is_readable($filename));

        $content = @file_get_contents($filename);
        $json = [];

        Assert::type('string', $content);

        Assert::noError(static function () use ($content, &$json) {
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        });

        Assert::equal([
            'head' => [
                'branch' => 'master',
                'commit_hash' => '59d7c7f0e34f4db5ba7f5c28e3eb4630339d4382',
            ],
            'latest_tag' => [
                'name' => 'v0.2.1',
                'commit_hash' => '8f2c308e3a5330b7924634edd7aa38eec97a4114',
            ],
        ], $json);
    }

    private function removeFile(string $filename): void
    {
        if (file_exists($filename)) {
            @unlink($filename);
        }
    }
}

(new ExportRepositoryCommandTest())->run();
