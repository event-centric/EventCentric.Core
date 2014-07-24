<?php

namespace EventCentric\AggregateRoot;

use EventCentric\When\ConventionBased\ConventionBasedWhen;

/**
 * Extending this abstract class from your Aggregate Root provides implementations to:
 * - track changes to the state of your Aggregate using Domain Events
 * - reconstitute the state of your Aggregate conventional method for applying the history of Domain Events
 *
 * @package EventCentric\AggregateRoot
 * @uses \EventCentric\AggregateRoot\EventSourcing a trait that implements TrackChanges
 * @uses \EventCentric\AggregateRoot\Reconstitution a trait that implements ReconstitutesFromHistory
 * @uses \EventCentric\When\ConventionBased\ConventionBasedWhen a trait for reacting to Domain Events
 */
abstract class AggregateRootEntity implements AggregateRoot
{
    use EventSourcing, Reconstitution, ConventionBasedWhen;
    protected function __construct() {}
} 