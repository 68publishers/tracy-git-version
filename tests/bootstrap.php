<?php

declare(strict_types=1);

use Tester\Environment;

if (@!include __DIR__ . '/../vendor/autoload.php') {
    echo 'Install Nette Tester using `composer install`';
    exit(1);
}

Environment::setup();
Environment::bypassFinals();

# The lowest versions of some third-party dependencies trigger deprecations on newer PHP versions (used by CI only)
if (getenv('TESTS_IGNORE_DEPRECATIONS')) {
    error_reporting(E_ALL & ~E_DEPRECATED);
}
