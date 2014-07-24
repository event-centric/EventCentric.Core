<?php

namespace EventCentric\Identity;

interface Identity 
{
    /**
     * Creates an identifier object from a string representation
     * @param $string
     * @return Identity
     */
    public static function fromString($string);

    /**
     * Returns a string that can be parsed by fromString()
     * @return string
     */
    public function __toString();

    /**
     * Compares the object to another IdentifiesAggregate object. Returns true if both have the same type and value.
     * @param $other
     * @return boolean
     */
    public function equals(Identity $other);
}
