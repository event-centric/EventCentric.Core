<?php

namespace EventCentric\Tests\V2Persistence;

use EventCentric\Tests\Persistence\MySQLTestConnector;
use EventCentric\V2Persistence\MySQLPersistence;

final class MySQLPersistenceTest extends V2PersistenceTest
{
    protected function getPersistence()
    {
        $mysqlPersistence = new MySQLPersistence(MySQLTestConnector::connect());
        $mysqlPersistence->dropSchema();
        $mysqlPersistence->createSchema();
        return $mysqlPersistence;
    }
}