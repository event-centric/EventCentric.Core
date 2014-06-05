<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\Protection\AggregateRoot\AggregateRoot;
use EventCentric\Protection\AggregateRoot\EventSourcing;
use EventCentric\Protection\AggregateRoot\Reconstitution;
use EventCentric\When\ConventionBased\ConventionBasedWhen;

abstract class AggregateRootEntity implements AggregateRoot
{
    use \EventCentric\Protection\AggregateRoot\EventSourcing, \EventCentric\Protection\AggregateRoot\Reconstitution, ConventionBasedWhen;
    final private function __construct(){}
} 