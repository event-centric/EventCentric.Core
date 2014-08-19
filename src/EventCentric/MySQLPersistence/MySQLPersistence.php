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
use EventCentric\Identifiers\Identifier;
use EventCentric\MySQLPersistence\Query\Insert;
use EventCentric\MySQLPersistence\Query\MaxStreamRevision;
use EventCentric\Persistence\OptimisticConcurrencyFailed;
use EventCentric\Persistence\Persistence;
use Exception;

/**
 * An implementation of Persistence that stores Events in MySQL.
 */
final class MySQLPersistence implements Persistence
{
    const TABLE_NAME = 'events'; // @todo make configurable

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return EventEnvelope[]
     */
    public function fetch(Contract $streamContract, Identifier $streamId)
    {
        $records = $this->connection->fetchAll(
            Query\Select::from(self::TABLE_NAME),
            ['streamContract' => $streamContract, 'streamId' => $streamId]
        );

        $eventEnvelopes = array_map(
            function (array $record) {
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
     * @param Identifier $streamId
     * @param $expectedStreamRevision
     * @param EventEnvelope[] $eventEnvelopes
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     * @return void
     */
    public function commit(
        CommitId $commitId,
        Contract $streamContract,
        Identifier $streamId,
        $expectedStreamRevision,
        array $eventEnvelopes
    ) {
        $this->connection->beginTransaction();
        $now = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format("Y-m-d H:i:s");


        try {
            $this->controlOptimisticConcurrency($streamContract, $streamId, $expectedStreamRevision);

            $nextStreamRevision = $expectedStreamRevision;
            foreach ($eventEnvelopes as $eventEnvelope) {
                $this->connection->executeQuery(
                    Insert::into(self::TABLE_NAME),
                    [
                        'streamContract' => (string)$streamContract,
                        'streamId' => (string)$streamId,
                        'streamRevision' => ++$nextStreamRevision,
                        'eventContract' => (string)$eventEnvelope->getEventContract(),
                        'eventPayload' => (string)$eventEnvelope->getEventPayload(),
                        'eventId' => (string)$eventEnvelope->getEventId(),
                        'commitId' => $commitId,
                        'utcCommittedTime' => $now,
                    ]
                );
            }

            $this->connection->commit();

        } catch (Exception $exception) {
            $this->connection->rollback();
            throw $exception;
        }
    }

    public function createSchema()
    {
        $this->connection->executeQuery(
            Query\Create::table(self::TABLE_NAME)
        );
    }

    public function dropSchema()
    {
        $this->connection->executeQuery(
            Query\Drop::table(self::TABLE_NAME)
        );
    }

    /**
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @param $expectedStreamRevision
     * @throws \EventCentric\Persistence\OptimisticConcurrencyFailed
     */
    protected function controlOptimisticConcurrency(
        Contract $streamContract,
        Identifier $streamId,
        $expectedStreamRevision
    ) {
        $result = $this->connection->fetchArray(
            MaxStreamRevision::from(self::TABLE_NAME),
            ['streamContract' => $streamContract, 'streamId' => $streamId]
        );
        $actualStreamRevision = (int) $result[0];

        if ($actualStreamRevision != $expectedStreamRevision) {
            throw OptimisticConcurrencyFailed::revisionDoesNotMatch($expectedStreamRevision, $actualStreamRevision);
        }
    }
}
