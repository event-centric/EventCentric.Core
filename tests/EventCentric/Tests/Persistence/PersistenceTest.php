<?php

namespace EventCentric\Tests\Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\EventStore\EventId;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Persistence\OptimisticConcurrencyFailed;
use EventCentric\Persistence\Persistence;
use PHPUnit_Framework_TestCase;

final class PersistenceTest extends PHPUnit_Framework_TestCase
{
    use PersistenceProvider;

    /**
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function it_should_commit_and_fetch_events(Persistence $persistence)
    {
        $commitId = CommitId::generate();
        $streamContract = Contract::with('My.Contract');
        $streamId = OrderId::generate();

        $eventContract = Contract::with("My.SomethingHasHappened");
        $eventEnvelope1 = EventEnvelope::wrap(EventId::generate(), $eventContract, "My payload1");
        $eventEnvelope2 = EventEnvelope::wrap(EventId::generate(), $eventContract, "My payload2");
        $persistence->commit(
            $commitId,
            $streamContract,
            $streamId,
            $expectedStreamVersion = 0,
            [$eventEnvelope1, $eventEnvelope2]
        );

        $persistedEventEnvelopes = $persistence->fetch($streamContract, $streamId);

        $this->assertCount(2, $persistedEventEnvelopes);
        $this->assertTrue($persistedEventEnvelopes[0]->equals($eventEnvelope1));
        $this->assertTrue($persistedEventEnvelopes[1]->equals($eventEnvelope2));
    }

    /**
     * @test
     * @depends it_should_commit_and_fetch_events
     * @param Persistence $persistence
     */
    public function it_should_throw_when_events_have_been_committed_elsewhere(Persistence $persistence)
    {
        $commitId = CommitId::generate();
        $streamContract = Contract::with('My.Contract');
        $streamId = OrderId::generate();
        $eventEnvelope = EventEnvelope::wrap(EventId::generate(), Contract::with("My.SomethingHasHappened"), "My payload2");

        $this->setExpectedException(OptimisticConcurrencyFailed::class);

        $persistence->commit(
            $commitId,
            $streamContract,
            $streamId,
            $expectedStreamVersion = 1, // in fact two events where committed
            [$eventEnvelope]
        );


    }
}
 