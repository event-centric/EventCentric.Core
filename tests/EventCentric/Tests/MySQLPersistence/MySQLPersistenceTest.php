<?php

namespace EventCentric\Tests\MySQLPersistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\EventStore\EventId;
use EventCentric\Fixtures\OrderId;
use EventCentric\MySQLPersistence\MySQLPersistence;
use PHPUnit_Framework_TestCase;

final class MySQLPersistenceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MySQLPersistence
     */
    private $mysqlPersistence;

    protected function setUp()
    {
        parent::setUp();
        $this->mysqlPersistence = new MySQLPersistence(MySQLTestConnector::connect());
        $this->mysqlPersistence->dropSchema();
        $this->mysqlPersistence->createSchema();
    }

    /**
     * @test
     */
    public function it_should_commit_and_fetch_events()
    {
        $commitId = CommitId::generate();
        $streamContract = Contract::with('My.Contract');
        $streamId = OrderId::generate();

        $eventContract = Contract::with("My.SomethingHasHappened");
        $eventEnvelope1 = EventEnvelope::wrap(EventId::generate(), $eventContract, "My payload1");
        $eventEnvelope2 = EventEnvelope::wrap(EventId::generate(), $eventContract, "My payload2");
        $this->mysqlPersistence->commit($commitId, $streamContract, $streamId,
            [$eventEnvelope1, $eventEnvelope2]
        );

        $persistedEventEnvelopes = $this->mysqlPersistence->fetch($streamContract, $streamId);

        $this->assertCount(2, $persistedEventEnvelopes);
        $this->assertTrue($persistedEventEnvelopes[0]->equals($eventEnvelope1));
        $this->assertTrue($persistedEventEnvelopes[1]->equals($eventEnvelope2));
    }
}
 