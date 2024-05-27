<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'binary_operator_spaces' => ['operators' => ['=' => 'align_single_space']],
    ])
    ->setFinder($finder)
;
