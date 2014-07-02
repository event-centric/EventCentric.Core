<?php
namespace EventCentric\MySQLPersistence\Query;

final class Drop
{
    const QUERY = <<<MYSQL
DROP TABLE IF EXISTS `%s`;';
MYSQL;

    public static function drop($tableName)
    {
        return sprintf(self::QUERY, $tableName);
    }
}