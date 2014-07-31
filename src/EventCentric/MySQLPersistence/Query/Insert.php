<?php

namespace EventCentric\MySQLPersistence\Query;

final class Insert 
{
const QUERY = <<<MYSQL
INSERT INTO events
( streamContract,  streamId,  streamRevision,  eventContract,  eventPayload,  eventId,  commitId,  utcCommittedTime)
VALUES
(:streamContract, :streamId, :streamRevision, :eventContract, :eventPayload, :eventId, :commitId, :utcCommittedTime);
MYSQL;

    public static function into($tableName)
    {
        return sprintf(self::QUERY, $tableName);
    }
}
