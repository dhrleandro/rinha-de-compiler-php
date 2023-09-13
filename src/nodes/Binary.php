<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\BinaryOp;

class Binary implements Term
{
    public string $kind;
    public Term $lhs;
    public BinaryOp $op;
    public Term $rhs;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?Term $lhs,
        ?BinaryOp $op,
        ?Term $rhs,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->lhs = $lhs ?? UndefinedTerm::create();
        $this->op = $op ?? BinaryOp::create();
        $this->rhs = $rhs ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();
    }
}
