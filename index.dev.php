<?php

/**
 * Index de Desenvolvimento
 */

declare(strict_types=1);

use LeandroDaher\RinhaDeCompilerPhp\Compiler;
use LeandroDaher\RinhaDeCompilerPhp\TreeWalkInterpreter;
use LeandroDaher\RinhaDeCompilerPhp\VirtualMachine;

require_once __DIR__ . '/vendor/autoload.php';

// $file = 'sum.json';
$file = 'fib.json';
// $file = 'combination.json';
// $file = 'teste.json';

$astJsonFile = file_get_contents(__DIR__.'/var/files/'.$file);

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

$vm = new VirtualMachine($bytecode);
// $vm->printBytecode(false);exit;
// $vm->printBytecode(true);exit;
$delay = 0;
$debug = false;
$vm->start($delay, $debug);

echo "\nEND Bytecode Interpreter\n\n";

