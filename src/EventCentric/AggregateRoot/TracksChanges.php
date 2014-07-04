<?php

namespace EventCentric\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;

interface TracksChanges
{
    /**
     * Determine whether the object's state has changed since the last clearChanges();
     * @return bool
     */
    public function hasChanges();

    /**
     * Get all changes to the object since the last the last clearChanges();
     * @return DomainEvents
     */
    public function getChanges();

    /**
     * Clear all state changes from this object.
     * @return void
     */
    public function clearChanges();

} 