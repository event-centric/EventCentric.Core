<?php

namespace EventCentric\Tests\DomainEvents;

use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEvents;
use EventCentric\DomainEvents\DomainEventsAreImmutable;
use EventCentric\DomainEvents\DomainEventsArray;
use Exception;
use PHPUnit_Framework_TestCase;

final class DomainEventsArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_be_immutable()
    {
        $events = new DomainEventsArray([
            new SomethingHasHappened,
            new SomethingHasHappened,
        ]);

        $this->setExpectedException(DomainEventsAreImmutable::class);
        $events[] = new SomethingHasHappened();
    }

    /**
     * @test
     */
    public function it_should_allow_appends_from_different_implementations()
    {
        $events = new DomainEventsArray([
            new SomethingHasHappened,
            new SomethingHasHappened,
        ]);

        $appendedEvents = $events->append(
            new AlternativeDomainEventsImplementation([
                new SomethingHasHappened,
            ])
        );

        $this->assertCount(3, $appendedEvents);
        $this->assertCount(2,$events);
    }
}

final class SomethingHasHappened implements DomainEvent
{
}

final class AlternativeDomainEventsImplementation implements DomainEvents
{
    private $dummyEvents = [];

    public function __construct(array $domainEvents)
    {
        foreach ($domainEvents as $domainEvent) {
            if (!$domainEvent instanceof DomainEvent) {
                throw new \InvalidArgumentException("DomainEvent expected");
            }
            $this->dummyEvents[] = $domainEvent;
        }
    }


    final public function current()
    {
        return current($this->dummyEvents);
    }

    final public function key()
    {
        return key($this->dummyEvents);
    }

    final public function next()
    {
        next($this->dummyEvents);
    }

    final public function rewind()
    {
        reset($this->dummyEvents);
    }

    final public function valid()
    {
        return null !== key($this->dummyEvents);
    }

    final public function append(DomainEvents $other) { throw new Exception("Not implemented");}
    final public function count() { throw new Exception("Not implemented");}
    final public function offsetExists($offset) { throw new Exception("Not implemented");}
    final public function offsetGet($offset) { throw new Exception("Not implemented");}
    final public function offsetSet($offset, $value) { throw new Exception("Not implemented");}
    final public function offsetUnset($offset) { throw new Exception("Not implemented");}
    final public function map(Callable $callback) { throw new Exception("Not implemented");}
}