<?php

namespace EventCentric\EventStore;

use EventCentric\Identity\UuidIdentity;

/**
 * CommitId identifies the domain events that were persisted as part of the same transaction.
 * @package EventCentric\EventStore
 */
final class CommitId extends UuidIdentity
{

} 