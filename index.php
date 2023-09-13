<?php

declare(strict_types=1);

use LeandroDaher\RinhaDeCompilerPhp\TreeWalkPrint;

require_once __DIR__ . '/vendor/autoload.php';


echo "Rinha de Compiler PHP\n\n";

$astJsonFile = file_get_contents(__DIR__.'/files/sum.json');
$interpreter = new TreeWalkPrint($astJsonFile);
$interpreter->start();

echo "\n\nFIM Rinha de Compiler PHP\n";
