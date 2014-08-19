<?php

namespace EventCentric\EventStore;

use EventCentric\Identifiers\UuidIdentifier;

/**
 * CommitId identifies the Domain Events that were persisted as part of the same transaction.
 */
final class CommitId extends UuidIdentifier
{
}
