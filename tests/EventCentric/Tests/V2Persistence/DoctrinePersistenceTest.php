<?php

namespace EventCentric\Tests\V2Persistence;

use EventCentric\Tests\Persistence\MySQLTestConnector;
use EventCentric\V2Persistence\DoctrinePersistence;
use EventCentric\V2Persistence\Schema\Doctrine\EventStoreSchema;

final class DoctrinePersistenceTest extends V2PersistenceTest
{
    protected function getPersistence()
    {
        $connection = MySQLTestConnector::connect();

        $schema = new EventStoreSchema($connection, $_ENV['testsuite_db_eventstore_table']);
        $schema->dropSchema();
        $schema->createSchema();

        return new DoctrinePersistence($connection);
    }
}
