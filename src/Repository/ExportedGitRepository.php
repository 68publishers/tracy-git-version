<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use JsonException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\ExportedGitCommandHandlerInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetLatestTagCommandHandler;
use function file_get_contents;
use function is_readable;
use function json_decode;

final class ExportedGitRepository extends AbstractGitRepository
{
    private string $file;

    private string $source;

    /** @var array<mixed, mixed>|null */
    private ?array $json = null;

    private ?bool $valid = null;

    /**
     * @param array<class-string, GitCommandHandlerInterface> $handlers
     */
    public function __construct(string $file, array $handlers = [], string $source = self::SOURCE_EXPORT)
    {
        $this->file = $file;
        $this->source = $source;

        parent::__construct($handlers);
    }

    public static function createDefault(string $file): self
    {
        return new self($file, [
            GetHeadCommand::class => new GetHeadCommandHandler(),
            GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
        ]);
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function isAccessible(): bool
    {
        return $this->isValid();
    }

    public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
    {
        if ($handler instanceof ExportedGitCommandHandlerInterface && $this->isValid()) {
            $handler = $handler->withExportedValue($this->json ?? []);
        }

        parent::addHandler($commandClassname, $handler);
    }

    private function isValid(): bool
    {
        if (null !== $this->valid) {
            return $this->valid;
        }

        if (!is_readable($this->file)) {
            return $this->valid = false;
        }

        $content = @file_get_contents($this->file);

        if (false === $content) {
            return $this->valid = false;
        }

        try {
            $this->json = (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            return $this->valid = true;
        } catch (JsonException $e) {
            return $this->valid = false;
        }
    }
}
