<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;

// prefix _ because Print is reserved
class _Print implements Term
{
    public string $kind;
    public Term $value;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?Term $value,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->value = $value ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();
    }
}
