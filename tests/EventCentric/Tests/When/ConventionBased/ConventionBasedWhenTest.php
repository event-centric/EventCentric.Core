<?php

namespace EventCentric\Tests\When\Tests\ConventionBased;

use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEventsArray;
use EventCentric\When\ConventionBased\ConventionBasedWhen;

final class MyReactor
{
    use ConventionBasedWhen;

    private $reacted = false;

    protected function whenSomethingHasHappened(SomethingHasHappened $event)
    {
        $this->reacted = true;
    }

    public function test()
    {
        $domainEvents = new DomainEventsArray([new SomethingHasHappened()]);
        $this->whenAll($domainEvents);
        if(!$this->reacted) {
            throw new \Exception("The method 'whenSomethingHasHappened' was not called");
        }
    }
}

final class SomethingHasHappened implements DomainEvent
{}

$reactor = new MyReactor();
$reactor->test();