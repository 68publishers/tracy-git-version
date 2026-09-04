<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Export\PartialExporter;

use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\NearestTagExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetNearestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetNearestTagCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalGitRepository;
use SixtyEightPublishers\TracyGitVersion\Tests\GitHelper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class NearestTagExporterTest extends TestCase
{
    public function testExportNearestTag(): void
    {
        $repository = GitHelper::init();

        try {
            GitHelper::createFile($repository, 'file.txt', 'test');
            $repository->commit('first');
            $repository->createTag('v1.0.0');
            $taggedCommitId = $repository->getLastCommitId();
            GitHelper::createFile($repository, 'file2.txt', 'test');
            $repository->commit('second');

            $gitRepository = new LocalGitRepository(GitDirectory::createAutoDetected($repository->getRepositoryPath()), [
                GetNearestTagCommand::class => new GetNearestTagCommandHandler(null, true),
            ]);

            Assert::equal([
                'nearest_tag' => [
                    'name' => 'v1.0.0',
                    'commit_hash' => $taggedCommitId->toString(),
                    'distance' => 1,
                ],
            ], (new NearestTagExporter())->export(Config::create(), $gitRepository));
        } finally {
            GitHelper::destroy($repository);
        }
    }

    public function testExportWithoutTag(): void
    {
        $gitRepository = new LocalGitRepository(GitDirectory::createFromGitDirectory(__DIR__ . '/../../files/test-git-detached'), [
            GetNearestTagCommand::class => new GetNearestTagCommandHandler(),
        ]);

        Assert::equal([], (new NearestTagExporter())->export(Config::create(), $gitRepository));
    }
}

(new NearestTagExporterTest())->run();
