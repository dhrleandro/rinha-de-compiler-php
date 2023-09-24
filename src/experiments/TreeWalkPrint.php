<?php

namespace LeandroDaher\RinhaDeCompilerPhp;

class TreeWalkPrint
{
    private $ast;

    public function __construct($astJsonFile)
    {
        $this->ast = json_decode($astJsonFile, false);
    }

    private function emmit(string $str, int $indentation = 0)
    {
        if ($indentation > 0) {
            for ($i=0; $i < $indentation; $i++) {
                echo '.';
            }
        }
        echo "$str\n";
    }

    private function interpret($node, int $indentation = 0)
    {
        $this->emmit("$node->kind", $indentation);

        switch ($node->kind) {
            case 'Let':
                $name = $node->name->text;
                $this->emmit("$name ", $indentation+2);
                $this->interpret($node->value, $indentation+2);
                $this->interpret($node->next, $indentation+2);
                break;

            case 'Function':
                foreach ($node->parameters as $param) {
                    $this->emmit("$param->text ", $indentation+2);
                }
                $this->interpret($node->value, $indentation+2);
                break;

            case 'If':
                $this->emmit('CONDITION: ', $indentation+2);
                $this->interpret($node->condition, $indentation+4);
                $this->emmit('THEN: ', $indentation+2);
                $this->interpret($node->then, $indentation+4);
                $this->emmit('OTHERWISE: ', $indentation+2);
                $this->interpret($node->otherwise, $indentation+4);
                break;

            case 'Binary':
                $this->interpret($node->lhs, $indentation+2);
                $this->emmit($node->op, $indentation+2);
                $this->interpret($node->rhs, $indentation+2);
                break;

            case 'Int':
                $this->emmit("$node->value ", $indentation+2);
                break;

            case 'Var':
                $this->emmit("$node->text ", $indentation+2);
                break;

            case 'Call':
                $this->interpret($node->callee, $indentation+2);
                foreach ($node->arguments as $argument) {
                    $this->interpret($argument, $indentation+2);
                }
                break;

            case 'Print':
                $this->interpret($node->value, $indentation+2);
                break;

            default:
                throw new \Exception("Unknown node kind: $node->kind");
        }
    }

    public function start()
    {
        return $this->interpret($this->ast->expression);
    }
}
