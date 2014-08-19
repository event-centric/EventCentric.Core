<?php

namespace EventCentric\Persistence;

use EventCentric\Identifiers\Identifier;
use EventCentric\Contracts\Contract;

final class InMemoryRecord
{
    public $checkpointNumber;
    public $bucket = '@default';
    /** @var Contract */
    public $streamContract;
    public $eventContract;
    public $eventPayload;
    /** @var Identifier */
    public $streamId;
    public $streamRevision;
    public $utcCommittedTime;
    public $eventMetadataContract = '';
    public $eventMetadata = '';
    public $causationId = null;
    public $correlationId = null;
    public $eventId;
    public $commitId;
    public $commitSequence;
    public $dispatched;
}
