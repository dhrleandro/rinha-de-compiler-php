<?php

use LeandroDaher\RinhaDeCompilerPhp\Interpreter;

require_once __DIR__ . '/vendor/autoload.php';

echo "Rinha de Compiler PHP\n";
$interpreter = new Interpreter();
var_dump($interpreter);
