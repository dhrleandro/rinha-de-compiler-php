<?php

declare(strict_types=1);

use LeandroDaher\RinhaDeCompilerPhp\Compiler;
use LeandroDaher\RinhaDeCompilerPhp\TreeWalkInterpreter;
use LeandroDaher\RinhaDeCompilerPhp\LDHRVirtualMachine;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'sum.json';
$astJsonFile = file_get_contents(__DIR__.'/files/'.$file);

echo "Rinha de Compiler PHP\n\n";

echo "Compiling $file\n\n";
$compiler = new Compiler($astJsonFile);
$bytecode = $compiler->start();
// echo $bytecode;
echo "\nEND Bytecode Compiler\n\n";

echo "- - - - - - - - - - - - - - - -\n\n";


echo "Tree-Walk Interpreter $file\n\n";
$interpreter = new TreeWalkInterpreter($astJsonFile);
echo "Resultado: ".$interpreter->start();
echo "\nFIM\n\n";

echo "- - - - - - - - - - - - - - - -\n\n";


echo "Bytecode Interpreter $file\n\n";

$vm = new LDHRVirtualMachine($bytecode);
// $vm->printBytecode();
$delay = 0;//0.1;
$debug = false;//true;
$vm->interpret($delay, $debug);

echo "\nEND Bytecode Interpreter\n\n";

