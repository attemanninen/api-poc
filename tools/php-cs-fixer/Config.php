<?php

declare(strict_types=1);

use PhpCsFixer\Config;

$config = new Config();
$config->setRiskyAllowed(false);

return $config->setRules([
    '@PSR12' => true,
    '@Symfony' => true,
    'align_multiline_comment' => true,
    'array_indentation' => true,
    'blank_line_before_statement' => [
        'statements' => [
            'continue',
            'declare',
            'do',
            'exit',
            'for',
            'foreach',
            'goto',
            'if',
            'include',
            'include_once',
            'phpdoc',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
            'yield',
            'yield_from',
        ],
    ],
    'concat_space' => [
        'spacing' => 'one',
    ],
    'echo_tag_syntax' => false,
    'fully_qualified_strict_types' => [
        'phpdoc_tags' => [
            'param',
            'phpstan-param',
            'phpstan-property',
            'phpstan-property-read',
            'phpstan-property-write',
            'phpstan-return',
            'phpstan-var',
            'property',
            'property-read',
            'property-write',
            'psalm-param',
            'psalm-property',
            'psalm-property-read',
            'psalm-property-write',
            'psalm-return',
            'psalm-var',
            'return',
            'see',
            'throws',
            // We're intentionally disallowing 'var' in `phpdoc_tags` as it has
            // been witnessed to break Kiho API Annotations.
            // 'var',
        ],
    ],
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => null,
        'import_functions' => null,
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
    ],
    'method_chaining_indentation' => true,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'no_multi_line',
    ],
    'no_extra_blank_lines' => [
        'tokens' => [
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block',
            'switch',
            'throw',
            'use',
        ],
    ],
    'no_superfluous_phpdoc_tags' => true,
    'no_unneeded_control_parentheses' => true,
    'no_useless_return' => true,
    'nullable_type_declaration_for_default_null_value' => [
        'use_nullable_type_declaration' => true,
    ],
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_line_span' => true,
    'phpdoc_separation' => [
        'skip_unlisted_annotations' => true,
    ],
    'phpdoc_to_comment' => [
        'ignored_tags' => ['psalm-suppress'],
    ],
    'phpdoc_var_annotation_correct_order' => true,
    'single_line_throw' => false,
    'space_after_semicolon' => true,
    'standardize_increment' => false,
    'yoda_style' => false,
]);
