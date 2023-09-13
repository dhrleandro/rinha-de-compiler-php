<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

class BinaryOp
{
    const ADD = 'Add';
    const SUB = 'Subtract';
    const MUL = 'Multiply';
    const DIV = 'Divide';
    const REM = 'Rem';
    const EQ = 'Equal';
    const NEQ = 'Not equal';
    const LT = 'Less than';
    const GT = 'Greater than';
    const LTE = 'Less than or equal to';
    const GTE = 'Greater than or equal to';
    const AND = 'And';
    const OR = 'Or';

    const UNDEFINED = '';

    private string $operator;

    public function __construct(string $operator)
    {
        $operators = array(
            BinaryOp::ADD, BinaryOp::SUB, BinaryOp::MUL, BinaryOp::DIV,
            BinaryOp::REM, BinaryOp::EQ, BinaryOp::NEQ, BinaryOp::LT,
            BinaryOp::GT, BinaryOp::LTE, BinaryOp::GTE, BinaryOp::AND,
            BinaryOp::OR, BinaryOp::UNDEFINED
        );

        if (in_array($operator, $operators)) {
            $this->operator = $operator;
        } else {
            $this->operator = BinaryOp::UNDEFINED;
        }
    }

    public function getOp(): string
    {
        return $this->operator;
    }

    public static function create($operator = BinaryOp::UNDEFINED): BinaryOp
    {
        return new BinaryOp($operator);
    }
}
