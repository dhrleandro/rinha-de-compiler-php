<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;

class Parameter implements Term
{
    public function __construct()
    {
    }

    public static function create(): Parameter
    {
        return new Parameter();
    }
}
