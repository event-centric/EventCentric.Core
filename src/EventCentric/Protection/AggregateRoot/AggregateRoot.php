<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\Protection\AggregateRoot\ReconstitutesFromHistory;
use EventCentric\Protection\AggregateRoot\TracksChanges;

interface AggregateRoot extends TracksChanges, ReconstitutesFromHistory
{

}


