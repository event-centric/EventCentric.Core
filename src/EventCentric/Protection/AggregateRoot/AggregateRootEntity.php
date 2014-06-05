<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\When\ConventionBased\ConventionBasedWhen;

abstract class AggregateRootEntity implements AggregateRoot
{
    use EventSourcing, Reconstitution, ConventionBasedWhen;
    protected function __construct() {}
} 