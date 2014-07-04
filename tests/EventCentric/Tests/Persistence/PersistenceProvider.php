<?php

namespace EventCentric\Tests\Persistence;

use EventCentric\EventStore\EventStore;
use EventCentric\MySQLPersistence\MySQLPersistence;
use EventCentric\Persistence\InMemoryPersistence;
use EventCentric\Persistence\Persistence;
use EventCentric\Serializer\PhpDomainEventSerializer;
use EventCentric\UnitOfWork\ClassNameBasedAggregateRootReconstituter;
use EventCentric\UnitOfWork\UnitOfWork;

trait PersistenceProvider
{
    /**
     * @param Persistence $persistence
     * @return UnitOfWork
     */
    protected static function buildUnitOfWork(Persistence $persistence)
    {
        $eventStore = new EventStore($persistence);
        $eventSerializer = new PhpDomainEventSerializer();
        $aggregateRootReconstituter = new ClassNameBasedAggregateRootReconstituter();
        $unitOfWork = new UnitOfWork($eventStore, $eventSerializer, $aggregateRootReconstituter);
        return $unitOfWork;
    }

    public static function providePersistence()
    {
        $mysqlPersistence = new MySQLPersistence(MySQLTestConnector::connect());
        $mysqlPersistence->dropSchema();
        $mysqlPersistence->createSchema();
        $inMemoryPersistence = new InMemoryPersistence();

        return [
            'MySQLPersistence' => [$mysqlPersistence],
            'InMemoryPersistence' => [$inMemoryPersistence],
        ];
    }
}