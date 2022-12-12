<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Bridge\Tracy;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Helpers;

require __DIR__ . '/../../bootstrap.php';

final class HelpersTest extends TestCase
{
	public function testTemplateRendering(): void
	{
		$content = Helpers::renderTemplate(__DIR__ . '/../../files/templates/test.phtml', [
			'foo' => 'FOO',
		]);

		Assert::contains("<strong>FOO</strong>", $content);
	}
}

(new HelpersTest())->run();
