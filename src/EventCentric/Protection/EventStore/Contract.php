<?php

namespace EventCentric\Protection\EventStore;

/**
 * Generic VO for contracts, such as the contract name of an AggregateRoot
 * Typically, you use a naming convention like "My.Namespace.AggregateName"
 */
final class Contract
{
    /** @var string */
    private $name;

    public function __construct($name)
    {
        \Assert\that($name)->string()->notEmpty();
        $this->name = $name;
    }

    /** @return string */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param Contract $other
     * @return bool
     */
    public function equals(Contract $other)
    {
        return $this->name == $other->name;
    }
}