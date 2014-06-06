<?php


namespace EventCentric\Protection\EventStore;

use EventCentric\Identity\Identity;
use EventCentric\Identity\Uuid\UuidIdentity;

final class EventId implements Identity
{
    use UuidIdentity;

} 