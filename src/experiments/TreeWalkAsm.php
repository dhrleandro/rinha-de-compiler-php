<?php

namespace LeandroDaher\RinhaDeCompilerPhp;

class TreeWalkAsm
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
        $this->functions = '';
    }

    // &$output is a string value, but & pass with reference
    private function emmit(string &$output, string $command, int $indentation = 0, bool $breakLine = true)
    {
        if ($indentation > 0) {
            for ($i=0; $i < $indentation; $i++) {
                echo ' ';
            }
        }
        if ($breakLine) {
            $output .= "$command\n";
        } else {
            $output .= "$command";
        }
    }

    private function interpret($node, $lastNode = null): string
    {
        $output = '';

        switch ($node->kind) {
            case 'Let':
                $ident = $node->name->text;
                $this->emmit($output, 'DEF '.$ident);

                if ($node->value->kind === 'Function') {
                    $this->variables[$ident] = 'FUNCTION';
                    $this->emmit($this->functions, $this->interpret($node->value));
                    $this->emmit($output, "MOV $ident FUNCTION$this->functionIndex");
                } else {
                    $this->variables[$ident] = 'VARIABLE';
                    $this->emmit($output, $this->interpret($node->value));
                    $this->emmit($output, "MOV $ident AX");
                }

                $this->emmit($output, $this->interpret($node->next));
                return $output;
                break;

            case 'Function':
                $this->functionIndex++;
                $this->emmit($output, "FUNCTION$this->functionIndex:");
                foreach ($node->parameters as $param) {
                    $this->emmit($output, "DEF $param->text");
                    $this->emmit($output, "POP_SP $param->text ; pop param from top of SP (stack of params)");
                }
                $lastNode = $node;
                $this->emmit($output, $this->interpret($node->value, $lastNode));
                $this->emmit($output, "RET");
                return $output;
                break;

            case 'If':
                $this->emmit($output, '; - - - - IF - - - -');

                $this->ifIndex++;
                $this->emmit($output, 'IF'.$this->ifIndex.':');

                // CONDITION:
                $this->emmit($output, $this->interpret($node->condition));
                $this->emmit($output, "JT AX THEN$this->ifIndex ; jump to then if AX is 1 (true)");
                $this->emmit($output, "JF AX OTHERWISE$this->ifIndex ; jump to otherwise if AX is 0 (false)");

                // THEN:
                $this->emmit($output, 'THEN'.$this->ifIndex.':');
                $this->emmit($output, $this->interpret($node->then, $lastNode));
                $this->emmit($output, "JMP ENDIF$this->ifIndex ; jump to endif (unconditional)");

                // OTHERWISE:
                if ($node->otherwise) {
                    $this->emmit($output, 'OTHERWISE'.$this->ifIndex.':');
                    $this->emmit($output, $this->interpret($node->otherwise, $lastNode));
                    $this->emmit($output, "JMP ENDIF$this->ifIndex ; jump to endif (unconditional)");
                }

                $this->emmit($output, 'ENDIF'.$this->ifIndex.':');
                $this->emmit($output, '; - - - - ENDIF - - - -');

                return $output;
                break;

            case 'Binary':
                $this->emmit($output, $this->interpret($node->lhs));
                $this->emmit($output, "PUSH_SR AX ; push AX value to top of SR (stack of registers)");
                $this->emmit($output, $this->interpret($node->rhs));
                $this->emmit($output, "MOV BX AX");
                $this->emmit($output, "POP_SR AX ; pop top of SR (stack of registers) value to AX register");


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
                    $this->emmit($output, "PUSH_SRET AX");
                    $this->emmit($output, "RET");
                }

                return $output;
                break;

            case 'Int':
                $this->emmit($output, "MOV AX $node->value");
                if (!is_null($lastNode) && $lastNode->kind == 'Function') {
                    $this->emmit($output, "PUSH_SRET AX");
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
                        $this->emmit($output, "PUSH_SRET AX");
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
                    $argumentAsm = $this->interpret($argument);
                    $this->emmit($output, "; argument $index");
                    $this->emmit($output, $argumentAsm);
                    $this->emmit($output, "PUSH_SP AX ; push param AX value to top of SP (stack of params)");
                }
                $this->emmit($output, "; CALL End arguments");
                $this->emmit($output, "CALL ".$this->interpret($node->callee));
                $this->emmit($output, "POP_SRET AX");

                return $output;
                break;

            case 'Print':
                $this->emmit($output, $this->interpret($node->value));
                $this->emmit($output, "PRINT AX");
                return $output;
                break;

            default:
                throw new \Exception("Unknown node kind: $node->kind");
        }
    }

    public function start()
    {
        $source = '';
        $this->emmit($source, 'MAIN:');
        $this->emmit($source, $this->interpret($this->ast->expression));
        $this->emmit($source, 'RET');
        $this->emmit($source, $this->functions);
        return $source;
    }
}
