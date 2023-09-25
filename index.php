<?php

/**
 * Index de Produção
 */

declare(strict_types=1);

error_reporting(E_ERROR | E_PARSE);

use LeandroDaher\RinhaDeCompilerPhp\Compiler;
use LeandroDaher\RinhaDeCompilerPhp\TreeWalkInterpreter;
use LeandroDaher\RinhaDeCompilerPhp\VirtualMachine;

require_once __DIR__ . '/vendor/autoload.php';

$docker = is_file("/.dockerenv");

if ($docker) {
    $file = '/var/rinha/source.rinha.json';
} else {
    $file = __DIR__.'/var/rinha/source.rinha.json';
}

$astJsonFile = file_get_contents($file);

if (!$astJsonFile) {
    echo "No such file $file\n";
    exit;
}

echo "Rinha de Compiler PHP\n\n";

echo "Este programa interpretará uma AST da linguagem Rinha de duas maneiras:\n";
echo "1 - Tree-walker Interpreter: interpreta a AST diretamente na memória\n";
echo "2 - Bytecode Interpreter: compila a AST em Bytecode e interpreta o bytecode diretamente na memória através de uma simples VM\n\n";
echo "Os dois métodos serão executados em sequência e deverão apresentar o mesmo resultado.\n";
echo "\n\n";


echo "= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =\n";
echo "Método 1: Tree-Walk Interpreter\n";
echo "Arquivo: $file\n";
echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";

$interpreter = new TreeWalkInterpreter($astJsonFile);
echo "RRESULTADO: \n";
echo $interpreter->start();
echo "\n\n";
echo "= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =\n\n\n\n";


echo "= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =\n";
echo "Método 2: Bytecode Interpreter\n";
echo "Arquivo: $file\n";
echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";

echo "Compiling....";
$compiler = new Compiler($astJsonFile);
$bytecode = $compiler->start();
echo " Ok!\n\n";

$vm = new VirtualMachine($bytecode);
$delay = 0;
$debug = false;

echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";
echo "Inline Bytecode ( \"|\" significa \"\\n\"):\n";
echo "\n";
echo $vm->printInlineBytecode();
echo "\n\n";
echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";

echo "Bytecode Interpreter\n\n";

echo "RESULTADO:\n";
$vm->start($delay, $debug);

echo "\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";

echo "END Bytecode Interpreter\n\n";
echo "= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =\n\n";

