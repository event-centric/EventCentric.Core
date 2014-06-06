<?php

namespace EventCentric\Protection\Tests\EventStore;

use EventCentric\Protection\EventStore\CommitId;
use EventCentric\Protection\EventStore\EventId;
use EventCentric\Protection\EventStore\EventContext;
use EventCentric\Protection\EventStore\InMemoryEventStore;
use EventCentric\Protection\EventStore\Contract;
use EventCentric\Protection\Tests\Sample\OrderId;
use EventCentric\Protection\Tests\Sample\PaymentWasMade;
use EventCentric\Protection\Tests\Sample\ProductId;
use EventCentric\Protection\Tests\Sample\ProductWasOrdered;
use PHPUnit_Framework_TestCase;

final class InMemoryEventStoreTest extends PHPUnit_Framework_TestCase
{
    /** @var InMemoryEventStore */
    private $eventStore;
    private $orderId2;
    private $orderId1;
    private $events;

    protected function setUp()
    {
        parent::setUp();

        $this->eventStore = new InMemoryEventStore();
        $this->orderId1 = OrderId::generate();
        $this->orderId2 = OrderId::generate();
        $this->events = [
            new ProductWasOrdered($this->orderId1, ProductId::generate(), 100),
            new PaymentWasMade($this->orderId1, 100),
            new ProductWasOrdered($this->orderId2, ProductId::generate(), 200),
        ];

    }

    /**
     * @test
     */
    public function it_should_only_return_events_for_the_correct_contract()
    {
        $contract = new Contract('My.Order');

        $eventContexts = [
            new EventContext($contract, $this->events[0]->getOrderId(), EventId::generate(), $this->events[0]),
            new EventContext($contract, $this->events[1]->getOrderId(), EventId::generate(), $this->events[1]),
            new EventContext($contract, $this->events[2]->getOrderId(), EventId::generate(), $this->events[2]),
        ];
        $this->eventStore->commit(CommitId::generate(), $eventContexts);

        $stream = $this->eventStore->getStreamByContract($contract, $this->orderId1);

        $this->assertCount(2, $stream->events());
        $this->assertEquals($this->events[0], $stream->events()[0]);
        $this->assertEquals($this->events[1], $stream->events()[1]);

    }
}
 