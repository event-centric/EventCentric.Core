<?php

namespace EventCentric\AggregateRoot;

use EventCentric\AggregateRoot\ReconstitutesFromHistory;
use EventCentric\AggregateRoot\TracksChanges;

/**
 * An AggregateRoot class is responsible for:
 * - tracking changes to the state of the Aggregate
 * - reconstituting the Aggregate from a history of DomainEvents
 */
interface AggregateRoot extends TracksChanges, ReconstitutesFromHistory
{

}


