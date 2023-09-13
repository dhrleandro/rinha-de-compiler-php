<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;

// prefix _ because If is reserved
class _If implements Term
{
    public string $kind;
    public Term $condition;
    public Term $then;
    public Term $otherwise;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?Term $condition,
        ?Term $then,
        ?Term $otherwise,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->condition = $condition ?? UndefinedTerm::create();
        $this->then = $then ?? UndefinedTerm::create();
        $this->otherwise = $otherwise ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();
    }
}
