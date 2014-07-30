<?php

namespace EventCentric\Tests\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventId;
use EventCentric\Identifiers\Identifier;
use EventCentric\Tests\Fixtures\Order;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Tests\Fixtures\PaymentWasMade;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;
use EventCentric\V2Persistence\Bucket;
use EventCentric\V2Persistence\V2Persistence;

abstract class V2PersistenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Contract
     */
    private $orderContract;

    /**
     * @var OrderId
     */
    private $anOrderId;

    /**
     * @var  PendingEvent
     */
    private $pendingEvent;

    /**
     * @var V2Persistence
     */
    private $persistence;

    /**
     * @return V2Persistence
     */
    abstract protected function getPersistence();

    protected function setUp()
    {
        parent::setUp();

        $this->orderContract = Contract::canonicalFrom(Order::class);
        $this->anOrderId = OrderId::generate();
        $this->pendingEvent = new PendingEvent(
            EventId::generate(),
            Bucket::defaultx(),
            $this->orderContract,
            $this->anOrderId,
            Contract::canonicalFrom(PaymentWasMade::class),
            '{"my":"payload"}'
        );
        $this->persistence = $this->getPersistence();
    }

    /**
     * @test
     */
    public function it_should_persist_and_fetch_event_an_event()
    {
        $this->persistence->persist($this->pendingEvent);

        $committedEvents = $this->persistence->fetchFromStream(Bucket::defaultx(), Contract::canonicalFrom(Order::class), $this->anOrderId);

        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_fetch_by_bucket()
    {
        $otherBucket = new Bucket('other.bucket');
        $eventInOtherBucket = new PendingEvent(EventId::generate(), $otherBucket, $this->orderContract, $this->anOrderId, Contract::canonicalFrom(PaymentWasMade::class), '{"my":"payload"}' );

        $this->persistence->persist($this->pendingEvent);
        $this->persistence->persist($eventInOtherBucket);

        $committedEvents = $this->persistence->fetchFromStream($otherBucket, Contract::canonicalFrom(Order::class), $this->anOrderId);
        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($eventInOtherBucket, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_fetch_by_stream_contract()
    {
        $otherStreamContract = Contract::with('other.stream');
        $eventWithOtherContract= new PendingEvent(EventId::generate(), Bucket::defaultx(), $otherStreamContract, $this->anOrderId, Contract::canonicalFrom(PaymentWasMade::class), '{"my":"payload"}' );

        $this->persistence->persist($this->pendingEvent);
        $this->persistence->persist($eventWithOtherContract);

        $committedEvents = $this->persistence->fetchFromStream(Bucket::defaultx(), $otherStreamContract, $this->anOrderId);
        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($eventWithOtherContract, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_fetch_by_stream_id()
    {
        $otherStreamId = OrderId::generate();
        $eventWithOtherStream = new PendingEvent(EventId::generate(), Bucket::defaultx(),$this->orderContract,$otherStreamId, Contract::canonicalFrom(PaymentWasMade::class), '{"my":"payload"}' );

        $this->persistence->persist($this->pendingEvent);
        $this->persistence->persist($eventWithOtherStream);

        $committedEvents = $this->persistence->fetchFromStream(Bucket::defaultx(),  Contract::canonicalFrom(Order::class), $otherStreamId);
        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($eventWithOtherStream, $committedEvents[0]);
    }

    private function assertCommittedEventMatchesPendingEvent(PendingEvent $pendingEvent, CommittedEvent $committedEvent)
    {
        $this->assertTrue($pendingEvent->getEventId()->equals($committedEvent->getEventId()));
        $this->assertTrue($pendingEvent->getStreamContract()->equals($committedEvent->getStreamContract()));
        $this->assertTrue($pendingEvent->getStreamId()->equals($committedEvent->getStreamId()));
        $this->assertTrue($pendingEvent->getEventContract()->equals($committedEvent->getEventContract()));
        $this->assertEquals($pendingEvent->getEventPayload(), $committedEvent->getEventPayload());
        $this->assertEquals($pendingEvent->getBucket(), $committedEvent->getBucket());

        if ($pendingEvent->hasEventMetadataContract() && $committedEvent->hasEventMetadataContract()) {
            $this->assertEquals($pendingEvent->getEventMetadataContract(), $committedEvent->getEventMetadataContract());
        }

        if ($pendingEvent->hasCausationId() && $committedEvent->hasCausationId()) {
            $this->assertEquals($pendingEvent->getCausationId(), $committedEvent->getCausationId());
        }

        if ($pendingEvent->hasCorrelationId() && $committedEvent->hasCorrelationId()) {
            $this->assertEquals($pendingEvent->getCorrelationId(), $committedEvent->getCorrelationId());
        }
    }
}
 