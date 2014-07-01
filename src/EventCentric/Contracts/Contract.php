<?php

namespace EventCentric\Contracts;

use Assert;

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

    public function __toString()
    {
        return $this->contract;
    }

    public function equals(Contract $other)
    {
        return $this->contract == $other->contract;
    }

} 