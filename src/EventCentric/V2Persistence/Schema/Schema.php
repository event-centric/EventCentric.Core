<?php

namespace EventCentric\V2Persistence\Schema;

/**
 * Interface Schema
 */
interface Schema
{
    public function createSchema();
    public function dropSchema();
    public function clearSchema();
}
