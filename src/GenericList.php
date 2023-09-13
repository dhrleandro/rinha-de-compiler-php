<?php

declare(strict_types=1);

namespace LeandroDaher\RinhaDeCompilerPhp;

class GenericList
{
    private string $allowedClassOrInterface;
    private array $list;

    public function __construct(string $allowedClassOrInterface, array $objects = []) {

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
        return class_exists($className, true) || interface_exists($className, true);
    }

    public function allowedClassOrAllowedInterfaceImplementation(mixed $item): bool
    {
        $class = get_class($item);
        $implements = class_implements($item, true);

        $listOfClassAndImplements = array_merge([$class], $implements);
        if (in_array($this->allowedClassOrInterface, $listOfClassAndImplements)) {
            return true;
        } else {
            return false;
        }
    }
}
