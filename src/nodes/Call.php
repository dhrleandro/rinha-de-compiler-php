<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

use LeandroDaher\RinhaDeCompilerPhp\Nodes\Term;
use LeandroDaher\RinhaDeCompilerPhp\Nodes\Location;
use LeandroDaher\RinhaDeCompilerPhp\GenericStack;

class Call implements Term
{
    public string $kind;
    public Term $callee;

    /**
     * List of Term
     * @var Term[]
     */
    public GenericStack $arguments;

    public Location $location;

    /**
     * @param null|string $kind
     * @param null|Term $callee
     * @param null|Term[] $arguments
     * @param null|Location $location
     * @return void
     */
    public function __construct(
        ?string $kind,
        ?Term $callee,
        ?GenericStack $arguments,
        ?Location $location
    ) {
        $this->kind = $kind ?? '';
        $this->callee = $callee ?? UndefinedTerm::create();
        $this->location = $location ?? Location::create();

        $argumentsTemp = $arguments ?? [];
        $this->arguments = new GenericStack(Term::class, $argumentsTemp);
    }
}
