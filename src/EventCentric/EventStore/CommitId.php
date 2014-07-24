<?php

namespace EventCentric\EventStore;

use EventCentric\Identifiers\UuidIdentifier;

/**
 * CommitId identifies the domain events that were persisted as part of the same transaction.
 * @package EventCentric\EventStore
 */
final class CommitId extends UuidIdentifier
{

} 