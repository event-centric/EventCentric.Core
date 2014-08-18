<?php

namespace EventCentric\Tests\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\EventId;
use EventCentric\Persistence\OptimisticConcurrencyFailed;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Tests\Fixtures\PaymentWasMade;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;
use EventCentric\V2Persistence\Bucket;
use EventCentric\V2Persistence\V2Persistence;

abstract class V2PersistenceTest extends \PHPUnit_Framework_TestCase
{
    use EventAssertions;

    /**
     * @var V2Persistence
     */
    private $persistence;

    private $eventContract;
    private $orderContract;
    private $aStreamId;
    private $amazonBucket;
    private $ebayBucket;
    private $invoiceContract;
    private $otherStreamId;

    /**
     * @var PendingEvent
     */
    private $pendingEvent1;

    /**
     * @var PendingEvent
     */
    private $pendingEvent2;

    /**
     * @var PendingEvent
     */
    private $pendingEvent3;

    /**
     * @var PendingEvent
     */
    private $pendingEvent4;

    /**
     * @var PendingEvent
     */
    private $pendingEvent5;

    /**
     * @return V2Persistence
     */
    abstract protected function getPersistence();

    protected function setUp()
    {
        parent::setUp();

        $this->amazonBucket = new Bucket('amazon');
        $this->ebayBucket = new Bucket('ebay');
        $this->orderContract = Contract::with('My.Order');
        $this->invoiceContract = Contract::with('My.Invoice');
        $this->aStreamId = OrderId::generate();
        $this->otherStreamId = OrderId::generate();
        $this->eventContract = Contract::canonicalFrom(PaymentWasMade::class);

        $this->pendingEvent1 = new PendingEvent(
            EventId::generate(),
            0,
            $this->amazonBucket,
            $this->orderContract,
            $this->aStreamId,
            $this->eventContract,
            '{"my":"payload"}'
        );

        $this->pendingEvent2 = new PendingEvent(
            EventId::generate(),
            0,
            $this->ebayBucket,
            $this->orderContract,
            $this->aStreamId,
            $this->eventContract,
            '{"my":"payload"}'
        );


        $this->pendingEvent3 = new PendingEvent(
            EventId::generate(),
            0,
            $this->amazonBucket,
            $this->invoiceContract,
            $this->aStreamId,
            $this->eventContract,
            '{"my":"payload"}'
        );

        $this->pendingEvent4 = new PendingEvent(
            EventId::generate(),
            0,
            $this->amazonBucket,
            $this->orderContract,
            $this->otherStreamId,
            $this->eventContract,
            '{"my":"payload"}'
        );

        $this->pendingEvent5 = new PendingEvent(
            EventId::generate(),
            1,
            $this->amazonBucket,
            $this->orderContract,
            $this->aStreamId,
            $this->eventContract,
            '{"my":"payload"}'
        );

        $this->persistence = $this->getPersistence();
    }

    /**
     * @test
     */
    public function it_should_commit_and_fetch_event_an_event()
    {
        $this->given_events_are_committed_individually();

        $committedEvents = $this->persistence->fetchFromStream($this->amazonBucket, $this->orderContract, $this->aStreamId);

        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent1, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_fetch_by_bucket()
    {
        $this->given_events_are_committed_individually();

        $committedEvents = $this->persistence->fetchFromStream($this->ebayBucket, $this->orderContract, $this->aStreamId);

        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent2, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_fetch_by_stream_contract()
    {
        $this->given_events_are_committed_individually();

        $committedEvents = $this->persistence->fetchFromStream($this->amazonBucket, $this->invoiceContract, $this->aStreamId);

        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent3, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_fetch_by_stream_id()
    {
        $this->given_events_are_committed_individually();

        $committedEvents = $this->persistence->fetchFromStream($this->amazonBucket, $this->orderContract, $this->otherStreamId);

        $this->assertCount(1, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent4, $committedEvents[0]);
    }

    /**
     * @test
     */
    public function it_should_give_the_same_commitId_to_events_committed_together()
    {
        $this->given_events_are_committed_together();

        $committedEvents = $this->persistence->fetchAll();
        $this->assertCount(4, $committedEvents);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent1, $committedEvents[0]);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent2, $committedEvents[1]);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent3, $committedEvents[2]);
        $this->assertCommittedEventMatchesPendingEvent($this->pendingEvent4, $committedEvents[3]);
    }

    /**
     * @test
     */
    public function it_should_give_incremental_commitSequences()
    {
        $this->given_two_commits();

        $committedEvents = $this->persistence->fetchAll();
        $this->assertEquals(1, $committedEvents[0]->getCommitSequence());
        $this->assertEquals(2, $committedEvents[1]->getCommitSequence());
        $this->assertEquals(3, $committedEvents[2]->getCommitSequence());
        $this->assertEquals(1, $committedEvents[3]->getCommitSequence());
    }

    /**
     * @test
     */
    public function it_should_give_incremental_checkpointNumbers()
    {
        $this->given_two_commits();

        $committedEvents = $this->persistence->fetchAll();

        // We use relative numbers because we can't guarantee the starting point, eg when using autoincrement in MySQL
        $checkpointNumber = $committedEvents[0]->getCheckpointNumber();
        $this->assertEquals(++$checkpointNumber, $committedEvents[1]->getCheckpointNumber());
        $this->assertEquals(++$checkpointNumber, $committedEvents[2]->getCheckpointNumber());
        $this->assertEquals(++$checkpointNumber, $committedEvents[3]->getCheckpointNumber());
    }

    /**
     * @test
     */
    public function it_should_give_incremental_streamRevisions_within_a_single_stream()
    {
        $this->given_events_are_committed_together();
        $this->given_event_is_committed_in_existing_stream();

        $committedEvents = $this->persistence->fetchFromStream($this->amazonBucket, $this->orderContract, $this->aStreamId);

        $this->assertCount(2, $committedEvents);
        $this->assertEquals(1, $committedEvents[0]->getStreamRevision());
        $this->assertEquals(2, $committedEvents[1]->getStreamRevision());

        $committedEvents = $this->persistence->fetchFromStream($this->ebayBucket, $this->orderContract, $this->aStreamId);
        $this->assertCount(1, $committedEvents);
        $this->assertEquals(1, $committedEvents[0]->getStreamRevision());
    }

    /**
     * @test
     */
    public function it_should_throw_when_expected_stream_revision_does_not_match()
    {
        $this->given_events_are_committed_together();

        $incorrectExpectedStreamRevision = 0;
        $pendingEvent = new PendingEvent(EventId::generate(), $incorrectExpectedStreamRevision, $this->amazonBucket, $this->orderContract, $this->aStreamId, $this->eventContract, '{"my":"payload"}');

        $this->setExpectedException(OptimisticConcurrencyFailed::class);
        $this->persistence->commit($pendingEvent);
    }

    /**
     * @test
     */
    public function it_should_delete_events_by_id()
    {
        $this->given_events_are_committed_together();
        $eventId = $this->pendingEvent1->getEventId();

        $this->assertCount(4, $this->persistence->fetchAll());
        $this->persistence->delete($eventId);
        $this->assertCount(3, $this->persistence->fetchAll());
    }

    /**
     * @test
     */
    public function it_should_delete_a_stream()
    {
        $this->given_events_are_committed_together();
        $eventId = $this->pendingEvent1->getEventId();

        $this->assertCount(4, $this->persistence->fetchAll());
        $this->persistence->deleteStream($this->amazonBucket, $this->orderContract, $this->otherStreamId);
        $this->assertCount(3, $this->persistence->fetchAll());
    }



    private function given_events_are_committed_individually()
    {
        $this->persistence->commit($this->pendingEvent1);
        $this->persistence->commit($this->pendingEvent2);
        $this->persistence->commit($this->pendingEvent3);
        $this->persistence->commit($this->pendingEvent4);
    }

    private function given_events_are_committed_together()
    {
        $this->persistence->commitAll(
            [
                $this->pendingEvent1,
                $this->pendingEvent2,
                $this->pendingEvent3,
                $this->pendingEvent4,
            ]
        );
    }

    private function given_two_commits()
    {
        $this->persistence->commitAll([
            $this->pendingEvent1,
            $this->pendingEvent2,
            $this->pendingEvent3,
        ]);
        $this->persistence->commit(
            $this->pendingEvent4
        );
    }

    private function given_event_is_committed_in_existing_stream()
    {
        $this->persistence->commit($this->pendingEvent5);
    }
}
 