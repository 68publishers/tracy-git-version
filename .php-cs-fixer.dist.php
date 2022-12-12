<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src')
	->in(__DIR__ . '/tests')
	->name(['*.php', '*.phpt'])
	->append([
		__DIR__ . '/tracy-git-version',
	]);

return (new PhpCsFixer\Config())
	->setUsingCache(false)
	->setIndent("\t")
	->setRules([
		'@PSR2' => true,
		'array_syntax' => ['syntax' => 'short'],
		'trailing_comma_in_multiline' => true,
		'constant_case' => [
			'case' => 'lower',
		],
		'declare_strict_types' => true,
		'phpdoc_align' => true,
		'blank_line_after_opening_tag' => true,
		'blank_line_before_statement' => [
			'statements' => ['break', 'continue', 'declare', 'return'],
		],
		'blank_line_after_namespace' => true,
		'single_blank_line_before_namespace' => true,
		'return_type_declaration' => [
			'space_before' => 'none',
		],
		'ordered_imports' => [
			'sort_algorithm' => 'length',
			'imports_order' => ['class', 'function', 'const'],
		],
		'no_unused_imports' => true,
		'single_line_after_imports' => true,
		'no_leading_import_slash' => true,
		'global_namespace_import' => [
			'import_constants' => true,
			'import_functions' => true,
			'import_classes' => true,
		],
		'concat_space' => [
			'spacing' => 'one',
		],
	])
	->setRiskyAllowed(true)
	->setFinder($finder);
