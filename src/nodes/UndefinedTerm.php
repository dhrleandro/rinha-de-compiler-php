<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

class UndefinedTerm implements Term
{
    public function __construct()
    {
    }

    public static function create(): UndefinedTerm
    {
        return new UndefinedTerm();
    }
}
