<?php

namespace EventCentric;

use EventCentric\Contracts\Contract;
use EventCentric\Identity\Identity;

interface Persistence
{
    public function fetch(Contract $streamContract, Identity $streamId);

    public function commit(Contract $streamContract, Identity $streamId, array $eventEnvelopes);
}