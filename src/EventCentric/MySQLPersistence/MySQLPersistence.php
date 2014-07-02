<?php

namespace EventCentric\MySQLPersistence;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\EventStore\EventId;
use EventCentric\Identity\Identity;
use EventCentric\Persistence\Persistence;
use Exception;

final class MySQLPersistence implements Persistence
{
    const TABLE_NAME = 'events';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $query;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->query = sprintf("SELECT * FROM %s WHERE streamContract = :streamContract AND streamId = :streamId ORDER BY utcCommittedTime ASC", self::TABLE_NAME);
    }

    /**
     * @param Contract $streamContract
     * @param Identity $streamId
     * @return EventEnvelope[]
     */
    public function fetch(Contract $streamContract, Identity $streamId)
    {
        $records = $this->connection->fetchAll($this->query, ['streamContract' => $streamContract, 'streamId' => $streamId]);

        $eventEnvelopes = array_map(
            function(array $record){
                return EventEnvelope::reconstitute(
                    EventId::fromString($record['eventId']),
                    Contract::with($record['eventContract']),
                    $record['eventPayload']
                );
            },
            $records
        );

        return $eventEnvelopes;
    }

    /**
     * @param CommitId $commitId
     * @param Contract $streamContract
     * @param Identity $streamId
     * @param EventEnvelope[] $eventEnvelopes
     * @throws ConnectionException
     * @throws Exception
     * @return void
     */
    public function commit(CommitId $commitId, Contract $streamContract, Identity $streamId, array $eventEnvelopes)
    {
        $this->connection->beginTransaction();
        try {

            $now = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format("Y-m-d H:i:s");

            foreach ($eventEnvelopes as $eventEnvelope) {
                $this->connection->insert(
                    self::TABLE_NAME,
                    [
                        'streamContract' => (string)$streamContract,
                        'streamId' => (string)$streamId,
                        'streamRevision' => 0,
                        'eventContract' => (string)$eventEnvelope->getEventContract(),
                        'eventPayload' => (string)$eventEnvelope->getEventPayload(),
                        'eventId' => (string)$eventEnvelope->getEventId(),
                        'commitId' => $commitId,
                        'utcCommittedTime' => $now,
                    ]
                );
            }

            $this->connection->commit();

        } catch(Exception $exception) {
            $this->connection->rollback();
            throw $exception;
        }
    }
}