<?php

namespace EventCentric\V2Persistence;

use Doctrine\DBAL\Connection;
use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;
use EventCentric\Persistence\OptimisticConcurrencyFailed;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;

final class DoctrinePersistence implements V2Persistence
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Commit a single event
     * @param PendingEvent $pendingEvent
     * @return void
     */
    public function commit(PendingEvent $pendingEvent)
    {
        throw new \Exception("Not implemented: ".__METHOD__);
    }

    /**
     * Commit a set of events in a transaction.
     * @param PendingEvent[] $pendingEvents
     * @throws OptimisticConcurrencyFailed
     * @return void
     */
    public function commitAll($pendingEvents)
    {
        throw new \Exception("Not implemented: ".__METHOD__);
    }

    /**
     * @param Bucket $bucket
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return CommittedEvent[]
     */
    public function fetchFromStream(Bucket $bucket, Contract $streamContract, Identifier $streamId)
    {
        return [];
    }

    /**
     * @return CommittedEvent[]
     */
    public function fetchAll()
    {
        throw new \Exception("Not implemented: ".__METHOD__);
    }
}
