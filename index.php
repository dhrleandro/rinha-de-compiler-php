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

// $docker = is_file("/.dockerenv");
$file = './source.rinha.json';
$astJsonFile = file_get_contents($file);

if (!$astJsonFile) {
    echo "No such file $file\n";
    exit;
}

echo "Rinha de Compiler PHP\n\n";

echo "Este programa interpretará uma AST da linguagem Rinha de duas maneiras:\n";
echo "1 - Tree-walker Interpreter: interrpeta a AST diretamente na memória\n";
echo "2 - Bytecode Interpreter: compila a AST em Bytecode e interpreta o bytecode diretamente na memória através de uma simples VM\n";
echo "\n\n";


echo "= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =\n";
echo "Método 1: Tree-Walk Interpreter\n";
echo "Arquivo: $file\n";
echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";

$interpreter = new TreeWalkInterpreter($astJsonFile);
echo "Resultado: ".$interpreter->start();
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
echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n\n";

echo "Bytecode Interpreter\n\n";

echo "Resultado:\n";
$vm = new VirtualMachine($bytecode);
$delay = 0;
$debug = false;
$vm->start($delay, $debug);

echo "END Bytecode Interpreter\n\n";
echo "= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =\n\n";

