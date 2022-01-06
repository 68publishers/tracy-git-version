<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersionPanel\Tests\Cases\Export;

use Tester\Assert;
use Tester\TestCase;
use SixtyEightPublishers\TracyGitVersionPanel\Export\Config;
use SixtyEightPublishers\TracyGitVersionPanel\Exception\ExportConfigException;

require __DIR__ . '/../../bootstrap.php';

final class ConfigTest extends TestCase
{
	public function testBehaviourOfOptionsMethods(): void
	{
		$config = Config::create();

		$config->setOption('opt_1', 'value_1');
		$config->setOption('opt_2', 'value_2');
		$config->setOption('opt_3', 'value_3_1');

		# basic get
		Assert::true($config->hasOption('opt_1'));
		Assert::true($config->hasOption('opt_2'));
		Assert::true($config->hasOption('opt_3'));
		Assert::same('value_1', $config->getOption('opt_1'));
		Assert::same('value_2', $config->getOption('opt_2'));
		Assert::same('value_3_1', $config->getOption('opt_3'));

		# missing option
		Assert::false($config->hasOption('opt_4'));

		Assert::exception(static function () use ($config) {
			$config->getOption('opt_4');
		}, ExportConfigException::class, sprintf('Missing the option opt_4 in export config.'));

		# merge options => creates an array
		$config->mergeOption('opt_3', 'value_3_2');

		Assert::equal(['value_3_1', 'value_3_2'], $config->getOption('opt_3'));

		# merge options with an array
		$config->mergeOption('opt_3', [
			'value_3_3',
			'value_3_4',
		]);

		Assert::equal(['value_3_1', 'value_3_2', 'value_3_3', 'value_3_4'], $config->getOption('opt_3'));

		# merge new option (creates array)
		$config->mergeOption('opt_4', 'value_4');

		Assert::equal(['value_4'], $config->getOption('opt_4'));

		# override options
		$config->setOption('opt_2', 'foo');
		$config->setOption('opt_3', 'bar');

		Assert::same('foo', $config->getOption('opt_2'));
		Assert::same('bar', $config->getOption('opt_3'));
	}
}

(new ConfigTest())->run();
