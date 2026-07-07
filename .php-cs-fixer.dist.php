<?php

declare(strict_types=1);

use PhpCsFixer\{Config, Finder};

$finder = (new Finder())
	->in(__DIR__.'/src')
	->in(__DIR__.'/tests');

return (new Config())
	->setRiskyAllowed(true)
	->setIndent("\t")
	->setLineEnding("\n")
	->setRules([
		'@Symfony' => true,
		'array_syntax' => ['syntax' => 'short'],
		'blank_lines_before_namespace' => false,
		'blank_line_before_statement' => [
			'statements' => []
		],
		'blank_line_between_import_groups' => false,
		'braces_position' => [
			'classes_opening_brace' => 'same_line',
			'control_structures_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line',
		],
		'cast_spaces' => ['space' => 'none'],
		'concat_space' => ['spacing' => 'none'],
		'elseif' => true,
		'function_declaration' => [
			'closure_fn_spacing' => 'none',
			'closure_function_spacing' => 'none',
		],
		'group_import' => true,
		'increment_style' => false,
		'indentation_type' => true,
		'no_spaces_after_function_name' => true,
		'no_spaces_around_offset' => true,
		'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
		'no_unneeded_control_parentheses' => [
			'statements' => ['break', 'clone', 'continue', 'echo_print', 'others', 'switch_case', 'yield', 'yield_from']
		],
		'no_whitespace_in_blank_line' => false,
		'phpdoc_align' => ['align' => 'left'],
		'phpdoc_annotation_without_dot' => false,
		'phpdoc_no_alias_tag' => false,
		'phpdoc_separation' => false,
		'phpdoc_summary' => false,
		'phpdoc_to_param_type' => true,
		'phpdoc_to_property_type' => true,
		'phpdoc_to_return_type' => true,
		'protected_to_private' => false,
		'single_import_per_statement' => false,
		'single_line_after_imports' => true,
		'single_quote' => false,
		'single_space_around_construct' => [
			'constructs_followed_by_a_single_space' => [
				'abstract', 'as', 'attribute', 'break', 'case', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'enum', 'extends', 'final', 'finally', 'function', 'function_import', 'global', 'goto', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'throw', 'trait', 'try', 'use', 'use_lambda', 'use_trait', 'var', 'yield', 'yield_from'
			],
		],
		'single_line_empty_body' => true,
		'single_trait_insert_per_statement' => false,
		'trailing_comma_in_multiline' => false,
		'yoda_style' => false
	])
	->setFinder($finder);
