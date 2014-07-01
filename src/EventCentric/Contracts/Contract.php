<?php

namespace EventCentric\Contracts;

use Assert;
use Verraes\ClassFunctions\ClassFunctions;

final class Contract
{
    /**
     * @var string
     */
    private $contract;

    private function __construct($contract)
    {
        $this->contract = $contract;
    }

    /**
     * @param $name
     * @return Contract
     */
    public static function with($name)
    {
        Assert\that($name)->string()->betweenLength(1, 255);
        return new static($name);
    }

    /**
     * Make a contract from an object's namespace using dots instead of backslashes
     * @param $object
     * @return Contract
     */
    public static function canonicalFrom($object)
    {
        return new Contract(ClassFunctions::canonical($object));
    }

    public function __toString()
    {
        return $this->contract;
    }

    public function equals(Contract $other)
    {
        return $this->contract == $other->contract;
    }

} 