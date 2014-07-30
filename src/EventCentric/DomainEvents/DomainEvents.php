<?php

namespace EventCentric\DomainEvents;

use ArrayAccess;
use Countable;
use Iterator;

interface DomainEvents extends Countable, Iterator, ArrayAccess
{
    /**
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback);

    /**
     * @param DomainEvents $other
     * @return DomainEvents
     */
    public function append(DomainEvents $other);
}
