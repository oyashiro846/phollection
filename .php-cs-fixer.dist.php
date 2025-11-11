<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        '@PHP80Migration' => true,
        '@PHP81Migration' => true,
        '@PHP82Migration' => true,

        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
        ],
        'blank_line_before_statement' => [
            'statements' => ['return', 'if', 'for', 'foreach', 'while', 'switch', 'try'],
        ],
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'single_quote' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_trailing_comma_in_singleline' => true,
        'no_spaces_after_function_name' => true,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'parameters', 'arguments'],
        ],
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'throw',
                'use',
                'use_trait',
                'curly_brace_block',
                'square_brace_block',
                'parenthesis_brace_block',
            ],
        ],
        'fully_qualified_strict_types' => true, // 型はFQCN

        // ===== PHPDoc をなるべく揃える =====
        'phpdoc_align' => false,
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_types' => ['groups' => ['simple', 'meta']],
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_alias_tag' => true,
        // 厳しくすると消されることがある
        'no_superfluous_phpdoc_tags' => false,

        // ===== 厳しめにするための risky 系 =====
        'strict_param' => true,
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        'modernize_strpos' => true,
        'modernize_types_casting' => true,

        // ===== クラス/メソッド周り =====
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'one',
            ],
        ],
        'single_class_element_per_statement' => [
            'elements' => ['property'],
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],

        // ===== 文字列やコメント =====
        'heredoc_to_nowdoc' => true,
        'multiline_comment_opening_closing' => true,
        'single_line_comment_spacing' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],

        // ===== 余計なものをなくす =====
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_unneeded_curly_braces' => [
            'namespaces' => true,
        ],
        'simplified_null_return' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
            ],
            'sort_algorithm' => 'none',
        ],
    ]);
