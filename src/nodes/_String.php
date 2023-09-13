<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;

// prefix _ because String is reserved
class _String implements Term
{
    public string $kind;
    public string $value;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?string $value,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->value = $value ?? '';
        $this->location = $location ?? Location::create();
    }
}
