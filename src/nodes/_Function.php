<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Parameter;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;
use LeandroDaher\RinhaDeCompilerPhp\GenericStack;

// prefix _ because Function is reserved
class _Function implements Term
{
    public string $kind;

    /**
     * List of Parameter
     * @var Parameter[]
     */
    public GenericStack $parameters;


    public Term $value;
    public Location $location;

    public function __construct(
        ?string $kind,
        ?GenericStack $parameters,
        ?Term $value,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->value = $value ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();

        $parametersTemp = $parameters ?? [];
        $this->parameters = new GenericStack(Parameter::class, $parametersTemp);
    }
}
