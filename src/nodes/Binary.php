<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;

class Binary implements Term {
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
}
