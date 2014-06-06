<?php

namespace EventCentric\Aggregates\AggregateRoot;

use EventCentric\Aggregates\AggregateRoot\ReconstitutesFromHistory;
use EventCentric\Aggregates\AggregateRoot\TracksChanges;

interface AggregateRoot extends TracksChanges, ReconstitutesFromHistory
{

}


