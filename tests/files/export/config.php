<?php

declare(strict_types=1);

use SixtyEightPublishers\TracyGitVersionPanel\Export\Config;
use SixtyEightPublishers\TracyGitVersionPanel\Repository\LocalDirectory\GitDirectory;

return Config::createDefault()
	->setGitDirectory(GitDirectory::createFromGitDirectory(__DIR__ . '/../test-git'))
	->setOutputFile(TEMP_PATH . '/exports/git-repository-export.json');
