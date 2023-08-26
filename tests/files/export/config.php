<?php

declare(strict_types=1);

use SixtyEightPublishers\TracyGitVersion\Export\Config;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;

return Config::createDefault(false)
    ->setGitDirectory(GitDirectory::createFromGitDirectory(__DIR__ . '/../test-git'))
    ->setOutputFile(__DIR__ . '/../output/git-repository-export.json');
