<?php

namespace EventCentric\Identifiers;

use InvalidArgumentException;

abstract class StringIdentifier implements Identifier
{
    /**
     * @var string
     */
    private $string;

    public function __construct($string)
    {
        self::guardString($string);
        $this->string = $string;
    }

    /**
     * Creates an identifier object from a string representation
     * @param $string
     * @return static
     */
    public static function fromString($string)
    {
        return new static($string);
    }

    /**
     * Returns a string that can be parsed by fromString()
     * @return string
     */
    public function __toString()
    {
        return (string)$this->string;
    }

    /**
     * Compares the object to another IdentifiesAggregate object. Returns true if both have the same type and value.
     * @param Identifier $other
     * @return boolean
     */
    public function equals(Identifier $other)
    {
        return
            get_class($this) == get_class($other)
            && (string) $this->string == (string) $other;
    }

    private static function guardString($string)
    {
        if (!is_string($string) || empty($string)) {
            throw new InvalidArgumentException("String expected");
        }
    }
} 
