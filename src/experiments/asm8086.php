<?php

/**
 * Leandro Daher, 2023
 */

// 2+4-5
// AX = 1
// BX = 6
$code = "MOV AX, 2 
PUSH AX   
MOV AX, 4 
POP BX    
ADD AX, BX
PUSH AX   
MOV AX, 5 
POP BX    
SUB AX, BX
NEG AX";

$lines = explode("\n", $code);
$commands = array();
foreach($lines as $line) {
    $line = trim(str_replace(',', '', $line));
    $commands[] = explode(' ', $line);
}

// Interpretador

class Stack {
    private $stack;

    public function __construct($stack = [])
    {
        $this->stack = $stack;
    }

    public function push($value)
    {
        array_push($this->stack, $value);
    }

    public function pop(): mixed
    {
        return array_pop($this->stack);
    }

    public function print()
    {
        var_export($this->stack);
    }
}

class Register {
    const AX = 'AX';
    const BX = 'BX';
}

class OPCode {
    const MOV = 'MOV';
    const ADD = 'ADD';
    const SUB = 'SUB';
    const NEG = 'NEG';
    const PUSH = 'PUSH';
    const POP = 'POP';
}

$registersMemory = array(
    Register::AX => 0,
    Register::BX => 0
);

$stack = new Stack();

echo "Início\n";
var_dump($registersMemory);
foreach ($commands as $key => $cmd) {

    switch ($cmd[0]) {
        case OPCode::MOV:
            $register = $cmd[1];
            // se $cmd[2] for um registrador recupera seu valor
            if (in_array($cmd[2], array_keys($registersMemory))) {
                $value = intval($registersMemory[$cmd[2]]);
            } else { // se não: é um valor
                $value = intval($cmd[2]);
            }
            $registersMemory[$register] = $value;
            break;

        case OPCode::ADD:
            $register = $cmd[1];
            // se $cmd[2] for um registrador recupera seu valor
            if (in_array($cmd[2], array_keys($registersMemory))) {
                $value = $registersMemory[$cmd[2]];
            } else { // se não: é um valor
                $value = intval($cmd[2]);
            }
            $registersMemory[$register] += $value;
            break;

        case OPCode::SUB:
            $register = $cmd[1];
            // se $cmd[2] for um registrador recupera seu valor
            if (in_array($cmd[2], array_keys($registersMemory))) {
                $value = $registersMemory[$cmd[2]];
            } else { // se não: é um valor
                $value = intval($cmd[2]);
            }
            $registersMemory[$register] -= $value;
            break;

        case OPCode::NEG:
            $register = $cmd[1];
            $registersMemory[$register] *= -1;
            break;

        case OPCode::PUSH:
            $register = $cmd[1];
            $stack->push($registersMemory[$register]);
            break;
        case OPCode::POP:
            $register = $cmd[1];
            $registersMemory[$register] = $stack->pop();
            break;

        default:
            break;
    }
}

echo "\nFim\n";
var_dump($registersMemory);
