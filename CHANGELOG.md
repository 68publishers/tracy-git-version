# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-10-29

### Added

- Support for PHP 8.4 and 8.5 and for `symfony/console` 7 and 8.

### Changed

- Minimal PHP version is 8.3 (BC break).
- Minimal versions of `tracy/tracy` (`^2.10`), `nette/di` (`3.1.3`, conflict) and `symfony/console` (`5.4.47`, conflict), older releases do not run on PHP 8.3.
- The command `export-repository` is named by the attribute `#[AsCommand]` instead of the static property `$defaultName` (removed, BC break) and `configure()`. Lazy command loaders (e.g. `contributte/console` 0.10+) and `symfony/console` 7+ read the name from the attribute.
- The console application registers the command with `Application::addCommand()` on `symfony/console` 7.4+ (`add()` was removed in 8.0).
- Dev tooling: PHPStan 2 (level 7 in `phpstan.neon`), PHP-CS-Fixer 3.89+, Nette Tester 2.6+. The tests no longer hide deprecations.
- GitHub Actions run the tests on PHP 8.3, 8.4 and 8.5 with the highest dependencies and on PHP 8.3 with the lowest dependencies, with read-only token permissions. The Docker dev environment provides PHP 8.3, 8.4 and 8.5.

### Fixed

- The message of the exception thrown by `ResolvableGitRepository::addHandler()` had the names of the handler and the command swapped.

## [1.2.1] - 2025-01-11

### Fixed

- The command `export-repository` is named in `configure()` too.
- PHPStan errors.

## [1.2.0] - 2023-08-26

### Added

- Reading via the git binary: the method `GitDirectory::executeGitCommand()`, the boolean parameter `$useBinary` in the constructors of `GetHeadCommandHandler` and `GetLatestTagCommandHandler` and in the methods `LocalGitRepository::createDefault()` and `Export\Config::createDefault()`.

### Changed

- The Nette DI extensions register the command handlers with `useBinary = true` by default.

## [1.1.0] - 2022-12-12

### Added

- Dockerfile and docker-compose.yml with all supported PHP versions, Makefile, PHPStan and GitHub Actions.

### Changed

- Updated dev dependencies, coding style (removed unnecessary PHPDoc, `use` section for native functions) and README.

## [1.0.2] - 2022-05-19

### Fixed

- The directories `.` and `..` are skipped in `GetLatestTagCommandHandler`.

## [1.0.1] - 2022-04-30

### Fixed

- `GitDirectory::__toString()`.
- The path to the `tracy-git-version` binary in the PHP-CS-Fixer configuration and a link in README.

## [1.0.0] - 2022-01-09

First release.

[Unreleased]: https://github.com/68publishers/tracy-git-version-panel/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/68publishers/tracy-git-version-panel/compare/v1.2.1...v2.0.0
[1.2.1]: https://github.com/68publishers/tracy-git-version-panel/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/68publishers/tracy-git-version-panel/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/68publishers/tracy-git-version-panel/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/68publishers/tracy-git-version-panel/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/68publishers/tracy-git-version-panel/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/68publishers/tracy-git-version-panel/releases/tag/v1.0.0
