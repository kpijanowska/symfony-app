<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        // Keep spaces around the concatenation operator so php-cs-fixer agrees
        // with the PSR-12 ruleset used by PHP_CodeSniffer.
        'concat_space' => ['spacing' => 'one'],
        // Keep spaces around the union type separator "|" (incl. multi-catch),
        // again to match the PSR-12 ruleset used by PHP_CodeSniffer.
        'types_spaces' => ['space' => 'single', 'space_multiple_catch' => 'single'],
    ])
    ->setFinder($finder)
;
