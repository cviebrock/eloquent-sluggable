<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
    ->in(__DIR__);

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@PhpCsFixer'      => true,
        '@PHP84Migration'  => true,
        'indentation_type' => true,

        // Overrides for (opinionated) @PhpCsFixer and @Symfony rules:

        // Align "=>" in multi-line array definitions, unless a blank line exists between elements
        'binary_operator_spaces' => ['operators' => ['=>' => 'align_single_space_minimal']],

        // Subset of statements that should be proceeded with blank line
        'blank_line_before_statement' => ['statements' => ['case', 'continue', 'default', 'return', 'throw', 'try', 'yield', 'yield_from']],

        // Enforce space around concatenation operator
        'concat_space' => ['spacing' => 'one'],

        // Use {} for empty loop bodies
        'empty_loop_body' => ['style' => 'braces'],

        // Don't change any increment/decrement styles
        'increment_style' => false,

        // Forbid multi-line whitespace before the closing semicolon
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

        // Clean up PHPDocs, but leave @inheritDoc entries alone
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => false],

        // Ensure that traits are listed first in classes
        // (it would be nice to enforce more, but we'll start simple)
        'ordered_class_elements' => ['order' => ['use_trait']],

        // Ensure that param and return types are sorted consistently, with null at end
        'phpdoc_types_order' => ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last'],

        // Don't add @coversNothing annotations to tests
        'php_unit_test_class_requires_covers' => false,

        // Yoda style is too weird
        'yoda_style' => false,
    ])
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder);
