<?php
$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@PhpCsFixer:risky' => true,
            '@PSR12' => true,
            '@PSR12:risky' => true,
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
