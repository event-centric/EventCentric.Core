<?php

namespace EventCentric\MySQLPersistence\Query;

final class Select
{
    const QUERY = <<<MYSQL
SELECT * FROM `%s` WHERE streamContract = :streamContract AND streamId = :streamId ORDER BY utcCommittedTime ASC;
MYSQL;

    public static function from($tableName)
    {
        return sprintf(self::QUERY, $tableName);
    }
}
