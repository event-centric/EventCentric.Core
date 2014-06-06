<?php

namespace EventCentric\Protection\EventStore;

use EventCentric\Identity\Identity;
use EventCentric\Identity\Uuid\UuidIdentity;

/**
 * The id for a single atomic commit of DomainEvents
 */
final class CommitId implements Identity
{
    use UuidIdentity;
} 