<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Parameter;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;

// prefix _ because Function is reserved
class _Function implements Term
{
    public string $kind;

    /**
     * List of Parameter
     * @var GenericList<Parameter>
     */
    public GenericList $parameters;


    public Term $value;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?GenericList $parameters,
        ?Term $value,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->value = $value ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();

        $parametersTemp = $parameters ?? [];
        $this->parameters = new GenericList(Parameter::class, $parametersTemp);
    }
}
