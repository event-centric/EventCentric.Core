<?php

namespace EventCentric\V2Persistence;

/**
 * A Bucket is used to partition the EventStore. Typically, you'll use this for a logical partition in the domain, such
 * as tenants or accounts.
 */
final class Bucket
{
    private $name;

    public function __construct($name)
    {
        \Assert\that($name)->string()->betweenLength(1, 255);
        $this->name = $name;
    }

    public static function defaultx() // "default" is a reserved keyword :-(
    {
        return new Bucket('@default');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param Bucket $other
     * @return bool
     */
    public function equals(Bucket $other)
    {
        return $this->name === $other->name;
    }
} 