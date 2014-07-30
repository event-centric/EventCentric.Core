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
     * @var  PendingEvent
     */
    private $pendingEvent;

    /**
     * @var EventId
     */
    private $eventId;

    /**
     * @var Contract
     */
    private $streamContract;

    /**
     * @var Identifier
     */
    private $streamId;

    /**
     * @var Contract
     */
    private $eventContract;

    /**
     * @var string
     */
    private $eventPayload;

    /**
     * @var Bucket
     */
    private $bucket;

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

        $this->eventId = EventId::generate();
        $this->streamContract = Contract::canonicalFrom(Order::class);
        $this->streamId = OrderId::generate();
        $this->eventContract = Contract::canonicalFrom(PaymentWasMade::class);
        $this->eventPayload = '{"my":"payload"}';
        $this->bucket = Bucket::defaultx();
        $this->pendingEvent = new PendingEvent(
            $this->eventId,
            $this->bucket,
            $this->streamContract,
            $this->streamId,
            $this->eventContract,
            $this->eventPayload
        );
        $this->persistence = $this->getPersistence();
    }

    /**
     * @test
     */
    public function it_should_persist_and_fetch_event_an_event()
    {
        $commitId = CommitId::generate();
        $this->persistence->persist($commitId, $this->pendingEvent);

        $committedEvents = $this->persistence->fetchFromStream($this->bucket, $this->streamContract, $this->streamId);

        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent, $committedEvents[0]);
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
 