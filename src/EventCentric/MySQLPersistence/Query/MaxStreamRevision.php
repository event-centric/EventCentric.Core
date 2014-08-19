<?php
namespace EventCentric\MySQLPersistence\Query;

final class MaxStreamRevision
{
    const QUERY = <<<MYSQL
SELECT COALESCE(MAX(streamRevision), 0) FROM events
WHERE streamContract = :streamContract
AND   streamId = :streamId;
MYSQL;

    public static function from($tableName)
    {
        return sprintf(self::QUERY, $tableName);
    }
}
