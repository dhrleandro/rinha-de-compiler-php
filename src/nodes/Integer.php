<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;

class Integer implements Term
{
    public string $kind;
    public int $value;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?int $value,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->value = $value ?? 0;
        $this->location = $location ?? Location::create();
    }
}
