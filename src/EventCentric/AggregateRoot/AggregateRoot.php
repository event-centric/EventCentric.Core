<?php

namespace EventCentric\AggregateRoot;

use EventCentric\AggregateRoot\ReconstitutesFromHistory;
use EventCentric\AggregateRoot\TracksChanges;

interface AggregateRoot extends TracksChanges, ReconstitutesFromHistory
{

}


