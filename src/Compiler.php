<?php

namespace LeandroDaher\RinhaDeCompilerPhp;

class Compiler
{
    private $ast;
    private $variables;
    private $functionIndex;
    private $ifIndex;
    private $functions;

    public function __construct($astJsonFile)
    {
        $this->ast = json_decode($astJsonFile, false);

        $this->variables = [];
        $this->functionIndex = 0;
        $this->ifIndex = 0;
        $this->functions = [];
    }

    // &$output is a string value, but & pass with reference
    private function emmit(array &$output, string|array $command)
    {
        if (is_string($command)) {
            $output[] = $command;
        } else {
            $output = array_merge($output, $command);
        }

    }

    private function compile($node, $lastNode = null): array
    {
        $output = [];

        switch ($node->kind) {
            case 'Let':
                $ident = $node->name->text;

                if ($node->value->kind === 'Function') {

                    $this->variables[$ident] = 'FUNCTION';
                    $this->emmit($this->functions, $this->compile($node->value, $node));
                } else {

                    $this->emmit($output, 'DEF '.$ident);
                    $this->variables[$ident] = 'VARIABLE';
                    $this->emmit($output, $this->compile($node->value));
                    $this->emmit($output, "MOV $ident AX");
                }

                $this->emmit($output, $this->compile($node->next));
                return $output;
                break;

            case 'Function':
                if (is_null($lastNode) || !isset($lastNode->name->text)) {
                    throw new \Exception("Error Processing Request", 1);
                }

                $this->functionIndex++;
                $this->emmit($output, $lastNode->name->text.':');
                foreach ($node->parameters as $param) {
                    $this->emmit($output, "DEF $param->text");
                    $this->emmit($output, "POP $param->text ; pop param from top of SP (stack of params)");
                }

                $this->emmit($output, $this->compile($node->value, $node));
                $this->emmit($output, "RET");
                return $output;
                break;

            case 'If':
                $this->emmit($output, '; - - - - IF - - - -');

                $this->ifIndex++;
                $this->emmit($output, '.if'.$this->ifIndex.':');

                // CONDITION:
                $this->emmit($output, $this->compile($node->condition));
                $this->emmit($output, "JT AX .then$this->ifIndex ; jump to then if AX is 1 (true)");
                $this->emmit($output, "JF AX .otherwise$this->ifIndex ; jump to otherwise if AX is 0 (false)");

                // THEN:
                $this->emmit($output, '.then'.$this->ifIndex.':');
                $this->emmit($output, $this->compile($node->then, $lastNode));
                $this->emmit($output, "JMP .endif$this->ifIndex ; jump to endif (unconditional)");

                // OTHERWISE:
                if ($node->otherwise) {
                    $this->emmit($output, '.otherwise'.$this->ifIndex.':');
                    $this->emmit($output, $this->compile($node->otherwise, $lastNode));
                    $this->emmit($output, "JMP .endif$this->ifIndex ; jump to endif (unconditional)");
                }

                $this->emmit($output, '.endif'.$this->ifIndex.':');
                $this->emmit($output, '; - - - - ENDIF - - - -');

                return $output;
                break;

            case 'Binary':
                $this->emmit($output, $this->compile($node->lhs));
                $this->emmit($output, "PUSH AX ; push AX value to top of SR (stack of registers)");
                $this->emmit($output, $this->compile($node->rhs));
                $this->emmit($output, "MOV BX AX");
                $this->emmit($output, "POP AX ; pop top of SR (stack of registers) value to AX register");


                switch ($node->op) {
                    case 'Eq':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_EQ AX");
                        break;
                    case 'Neq':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_NEQ AX");
                        break;
                    case 'Lt':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_LT AX");
                        break;
                    case 'Gt':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_GT AX");
                        break;
                    case 'Lte':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_LTE AX");
                        break;
                    case 'Gte':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_GTE AX");
                        break;
                    case 'And':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_AND AX");
                        break;
                    case 'Or':
                        $this->emmit($output, "CMP AX BX");
                        $this->emmit($output, "SET_OR AX");
                        break;
                    case 'Add':
                        $this->emmit($output, "ADD AX BX");
                        break;
                    case 'Sub':
                        $this->emmit($output, "SUB AX BX");
                        break;
                    default:
                        throw new \Exception("Unknown operator $node->op");
                }

                if (!is_null($lastNode) && $lastNode->kind == 'Function') {
                    $this->emmit($output, "PUSH AX");
                    $this->emmit($output, "RET");
                }

                return $output;
                break;

            case 'Int':
                $this->emmit($output, "MOV AX $node->value");
                if (!is_null($lastNode) && $lastNode->kind == 'Function') {
                    $this->emmit($output, "PUSH AX");
                    $this->emmit($output, "RET");
                }
                return $output;
                break;

            case 'Var':
                if (isset($this->variables[$node->text]) && $this->variables[$node->text] === 'FUNCTION') {
                    if ($this->variables[$node->text] === 'FUNCTION') {
                        $this->emmit($output, "$node->text");
                    }
                } else {
                    $this->emmit($output, "MOV AX $node->text");
                    if (!is_null($lastNode) && $lastNode->kind == 'Function') {
                        $this->emmit($output, "PUSH AX");
                        $this->emmit($output, "RET");
                    }
                }

                return $output;
                break;

            case 'Call':
                $this->emmit($output, "; CALL Begin arguments");
                $invertedArguments = array_reverse($node->arguments, true);
                foreach ($invertedArguments as $key => $argument) {
                    $index = $key+1;
                    $argumentAsm = $this->compile($argument);
                    $this->emmit($output, "; argument $index");
                    $this->emmit($output, $argumentAsm);
                    $this->emmit($output, "PUSH AX ; push param AX value to top of SP (stack of params)");
                }
                $this->emmit($output, "; CALL End arguments");
                $callee = implode(' ', $this->compile($node->callee));
                $this->emmit($output, "CALL ".$callee);
                $this->emmit($output, "POP AX");

                return $output;
                break;

            case 'Print':
                $this->emmit($output, $this->compile($node->value));
                $this->emmit($output, "PRINT AX");
                return $output;
                break;

            default:
                throw new \Exception("Unknown node kind: $node->kind");
        }
    }

    public function start()
    {
        $source = [];
        $this->emmit($source, 'MAIN:');
        $this->emmit($source, $this->compile($this->ast->expression));
        $this->emmit($source, 'RET');
        $this->emmit($source, $this->functions);
        return implode("\n", $source);
    }
}
