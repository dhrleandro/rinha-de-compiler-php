<?php

declare(strict_types=1);

use LeandroDaher\RinhaDeCompilerPhp\LDHRVirtualMachine;
use LeandroDaher\RinhaDeCompilerPhp\TreeWalkInterpreter;
use LeandroDaher\RinhaDeCompilerPhp\TreeWalkAsm;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'sum.json';
$astJsonFile = file_get_contents(__DIR__.'/files/'.$file);

// use LeandroDaher\RinhaDeCompilerPhp\TreeWalkPrint;
// $interpreter = new TreeWalkPrint($astJsonFile);
// echo "Resultado: ".$interpreter->start();
// exit;

echo "Rinha de Compiler PHP\n\n";

echo "- - - - - - - - - - - - - - - -\n\n";

echo "Tree-Walk Interpreter $file\n\n";
$interpreter = new TreeWalkInterpreter($astJsonFile);
echo "Resultado: ".$interpreter->start();
echo "\nFIM\n\n";

echo "- - - - - - - - - - - - - - - -\n\n";

echo "Bytecode Compiler $file\n\n";
$compiler = new TreeWalkAsm($astJsonFile);
$bytecode = $compiler->start();
echo $bytecode;
echo "\nEND Bytecode Compiler\n\n";

echo "- - - - - - - - - - - - - - - -\n\n";

echo "Bytecode Interpreter $file\n\n";

$vm = new LDHRVirtualMachine($bytecode);
// $vm->printBytecode();
$vm->interpret();

echo "\nEND Bytecode Interpreter\n\n";
