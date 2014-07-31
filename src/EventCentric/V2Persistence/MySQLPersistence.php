<?php

namespace EventCentric\V2Persistence;

use Doctrine\DBAL\Connection;
use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;
use EventCentric\Persistence\OptimisticConcurrencyFailed;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;

final class MySQLPersistence implements V2Persistence
{
    const TABLE_NAME = 'events'; // @todo make configurable

    const CREATE = <<<MYSQL
CREATE TABLE `%s` (
  `checkpointNumber` bigint(20) NOT NULL AUTO_INCREMENT,
  `bucket` char(64) NOT NULL DEFAULT '@default',
  `streamContract` varchar(255) NOT NULL,
  `eventContract` varchar(255) NOT NULL,
  `eventPayload` text NOT NULL,
  `streamId` varchar(255) NOT NULL,
  `streamRevision` int(11) NOT NULL,
  `utcCommittedTime` DATETIME NOT NULL,
  `eventMetadataContract` varchar(255) NULL DEFAULT NULL,
  `eventMetadata` text DEFAULT NULL,
  `causationId` char(36) DEFAULT NULL,
  `correlationId` char(36) DEFAULT NULL,
  `eventId` char(36) NOT NULL,
  `commitId` char(36) NOT NULL,
  `commitSequence` int(11) NOT NULL,
  `dispatched` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`checkpointNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
MYSQL;

    const DROP = <<<MYSQL
DROP TABLE IF EXISTS `%s`;';
MYSQL;

    /**
     * @var Connection
     */
    private $connection;

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
    }

    /**
     * Commit a set of events in a transaction.
     * @param PendingEvent[] $pendingEvents
     * @throws OptimisticConcurrencyFailed
     * @return void
     */
    public function commitAll($pendingEvents)
    {
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
        return [];
    }

    public function createSchema()
    {
        $this->connection->executeQuery(
            sprintf(self::CREATE, self::TABLE_NAME)
        );
    }

    public function dropSchema()
    {
        $this->connection->executeQuery(
            sprintf(self::DROP, self::TABLE_NAME)
        );
    }
}