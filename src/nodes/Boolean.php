<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;

class Boolean implements Term
{
    public string $kind;
    public bool $value;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?bool $value,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->value = $value ?? false;
        $this->location = $location ?? Location::create();
    }
}
