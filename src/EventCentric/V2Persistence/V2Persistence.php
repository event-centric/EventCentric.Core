<?php

namespace EventCentric\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;
use EventCentric\V2EventStore\PendingEvent;

interface V2Persistence
{
    /**
     * @param PendingEvent $pendingEvent
     * @return void
     */
    public function persist(PendingEvent $pendingEvent);

    public function fetchFromStream(Bucket $bucket, Contract $streamContract, Identifier $streamId);
}