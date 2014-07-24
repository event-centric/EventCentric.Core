<?php

namespace EventCentric\EventStore;

use EventCentric\Identity\UuidIdentity;

/**
 * CommitId identifies the Domain Events that were persisted as part of the same transaction.
 */
final class CommitId extends UuidIdentity
{

} 