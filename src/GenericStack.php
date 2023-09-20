<?php

declare(strict_types=1);

namespace LeandroDaher\RinhaDeCompilerPhp;

class GenericStack
{
    private string $allowedClassOrInterface;
    private array $list;

    public function __construct(string $allowedClassOrInterface, array $objects = []) {

        $this->list = [];

        if (!$this->classExists($allowedClassOrInterface, true)) {
            throw new \Exception("Class name $allowedClassOrInterface not exists or not autoloaded", 1);
        }
        $this->allowedClassOrInterface = $allowedClassOrInterface;

        if (count($objects) > 0)
            $this->setList($objects);
    }

    public function push(mixed $item)
    {
        if ($this->AllowedClassOrAllowedInterfaceImplementation($item)) {
            $this->list[] = $item;
        } else {
            throw new \Exception("Cannot insert this object into this list of class '".$this->allowedClassOrInterface."' objects", 1);
        }
    }

    public function pop(): mixed
    {
        return array_pop($this->list);
    }

    public function setList(array $objects)
    {
        foreach ($objects as $object) {
            $this->push($object);
        }
    }

    public function getList()
    {
        return $this->list;
    }

    public function classExists(string $className): bool
    {
        return class_exists($className, true)   ||
            interface_exists($className, true)  ||
            in_array($className, ['integer', 'string']);
    }

    public function allowedClassOrAllowedInterfaceImplementation(mixed $item): bool
    {
        try {
            $class = get_class($item);
            $implements = class_implements($item, true);
        } catch(\Throwable $e) {
            $class = gettype($item);
            $implements = [];
        }


        $listOfClassAndImplements = array_merge([$class], $implements);
        if (in_array($this->allowedClassOrInterface, $listOfClassAndImplements)) {
            return true;
        } else {
            return false;
        }
    }
}
