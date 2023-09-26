<?php

namespace LeandroDaher\RinhaDeCompilerPhp;

class TreeWalkInterpreter
{
    private $ast;

    public function __construct($astJsonFile)
    {
        $this->ast = json_decode($astJsonFile, false);
    }

    private function interpret(/*Node*/ $node, array $environment = [])
    {
        switch ($node->kind) {
            case 'Call':
                $callee = $this->interpret($node->callee, $environment);
                $args = array_map(fn($arg) => $this->interpret($arg, $environment), $node->arguments);
                $newEnvironment = [...$environment];
                foreach ($callee->parameters as $index => $param) {
                    $newEnvironment[$param->text] = $args[$index];
                }
                return $this->interpret($callee->value, $newEnvironment);
            case 'Int':
            case 'Str':
                return $node->value;
            case 'Binary':
                $lhs = $this->interpret($node->lhs, $environment);
                $rhs = $this->interpret($node->rhs, $environment);
                switch ($node->op) {
                    case 'Add':
                        return $lhs + $rhs;
                    case 'Sub':
                        return $lhs - $rhs;
                    case 'Mul':
                        return $lhs * $rhs;
                    case 'Div':
                        if ($rhs === 0) {
                            // Dont trick me :)
                            throw new \Exception('Division by zero');
                        }
                        return $lhs / $rhs;
                    case 'Eq':
                        return $lhs == $rhs;
                    case 'Neq':
                        return $lhs != $rhs;
                    case 'Lt':
                        return $lhs < $rhs;
                    case 'Gt':
                        return $lhs > $rhs;
                    case 'Lte':
                        return $lhs <= $rhs;
                    case 'Gte':
                        return $lhs >= $rhs;
                    case 'And':
                        return $lhs && $rhs;
                    case 'Or':
                        return $lhs || $rhs;
                    default:
                        throw new \Exception("Unknown operator $node->op");
                }
            case 'Function':
                return $node;
            case 'Let':
                $value = $this->interpret($node->value, $environment);
                $environmentCopy = [...$environment];
                $environmentCopy[$node->name->text] = $value;
                return $this->interpret($node->next, $environmentCopy);
            case 'Print':
                $term = $this->interpret($node->value, $environment);
                switch (gettype($term)) {
                    case 'integer':
                    case 'string':
                        return $term;
                }
            case 'Var':
                return $environment[$node->text];
            case 'If':
                $condition = $this->interpret($node->condition, $environment);
                return $this->interpret($condition ? $node->then : $node->otherwise, $environment);
            default:
                throw new \Exception("Unknown node kind: $node->kind");
        }
    }

    public function start()
    {
        return $this->interpret($this->ast->expression, array());
    }
}
