<?php

namespace EventCentric\Contracts;

use Assert;

final class Contract
{
    /**
     * @var string
     */
    private $contractName;

    private function __construct($contract)
    {
        $this->contractName = $contract;
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
     * Make a contract from an fully qualified class name, of the form My.Namespace.Class
     * @param $className
     * @return Contract
     */
    public static function canonicalFrom($className)
    {
        return new Contract(
            str_replace('\\', '.', $className)
        );
    }

    public function toClassName()
    {
        return str_replace('.', '\\', $this->contractName);
    }

    public function __toString()
    {
        return $this->contractName;
    }

    public function equals(Contract $other)
    {
        return $this->contractName == $other->contractName;
    }

} 