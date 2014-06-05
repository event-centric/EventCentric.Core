<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\Protection\AggregateRoot\EventSourcing;
use EventCentric\Protection\AggregateRoot\Reconstitution;
use EventCentric\When\ConventionBased\ConventionBasedWhen;

abstract class AggregateRootEntity implements AggregateRoot
{
    use EventSourcing, Reconstitution, ConventionBasedWhen;
    final protected function __construct(){}
} 