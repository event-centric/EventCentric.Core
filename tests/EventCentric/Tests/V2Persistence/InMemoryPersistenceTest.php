<?php

namespace EventCentric\Tests\V2Persistence;

use EventCentric\V2Persistence\InMemoryPersistence;

final class InMemoryPersistenceTest extends V2PersistenceTest
{
    protected function getPersistence()
    {
        return new InMemoryPersistence();
    }
}