<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Parameter;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;

class Let implements Term
{
    public string $kind;
    public Parameter $name;
    public Term $value;
    public Term $next;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?Parameter $name,
        ?Term $value,
        ?Term $next,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->name = $name ?? Parameter::create();
        $this->value = $value ?? UndefinedTerm::create();
        $this->next = $next ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();
    }
}
