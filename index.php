<?php

/**
 * Index de Produção
 */

declare(strict_types=1);

error_reporting(E_ERROR | E_PARSE);

use LeandroDaher\RinhaDeCompilerPhp\Compiler;
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

$compiler = new Compiler($astJsonFile);
$bytecode = $compiler->start();
$vm = new VirtualMachine($bytecode);
$delay = 0;
$debug = false;
$vm->start($delay, $debug);
