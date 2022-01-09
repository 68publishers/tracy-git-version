<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Repository;

use JsonException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\GetLatestTagCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\Export\CommandHandler\ExportedGitCommandHandlerInterface;

final class ExportedGitRepository extends AbstractGitRepository
{
	private string $file;

	private string $source;

	private ?array $json = NULL;

	private ?bool $valid = NULL;

	/**
	 * @param string $file
	 * @param array  $handlers
	 * @param string $source
	 */
	public function __construct(string $file, array $handlers = [], string $source = self::SOURCE_EXPORT)
	{
		$this->file = $file;
		$this->source = $source;

		parent::__construct($handlers);
	}

	/**
	 * @param string $file
	 *
	 * @return static
	 */
	public static function createDefault(string $file): self
	{
		return new self($file, [
			GetHeadCommand::class => new GetHeadCommandHandler(),
			GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSource(): string
	{
		return $this->source;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAccessible(): bool
	{
		return $this->isValid();
	}

	/**
	 * {@inheritDoc}
	 */
	public function addHandler(string $commandClassname, GitCommandHandlerInterface $handler): void
	{
		if ($handler instanceof ExportedGitCommandHandlerInterface && $this->isValid()) {
			$handler = $handler->withExportedValue($this->json ?? []);
		}

		parent::addHandler($commandClassname, $handler);
	}

	/**
	 * @return bool
	 */
	private function isValid(): bool
	{
		if (NULL !== $this->valid) {
			return $this->valid;
		}

		if (!is_readable($this->file)) {
			return $this->valid = FALSE;
		}

		$content = @file_get_contents($this->file);

		if (FALSE === $content) {
			return $this->valid = FALSE;
		}

		try {
			$this->json = (array) json_decode($content, TRUE, 512, JSON_THROW_ON_ERROR);

			return $this->valid = TRUE;
		} catch (JsonException $e) {
			return $this->valid = FALSE;
		}
	}
}
