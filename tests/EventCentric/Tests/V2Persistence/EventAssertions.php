<?php

namespace EventCentric\Tests\V2Persistence;

use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;
use PHPUnit_Framework_Assert;

trait EventAssertions
{
    protected function assertCommittedEventMatchesPendingEvent(PendingEvent $pendingEvent, CommittedEvent $committedEvent)
    {
        PHPUnit_Framework_Assert::assertTrue($pendingEvent->getEventId()->equals($committedEvent->getEventId()));
        PHPUnit_Framework_Assert::assertTrue($pendingEvent->getStreamContract()->equals($committedEvent->getStreamContract()));
        PHPUnit_Framework_Assert::assertTrue($pendingEvent->getStreamId()->equals($committedEvent->getStreamId()));
        PHPUnit_Framework_Assert::assertTrue($pendingEvent->getEventContract()->equals($committedEvent->getEventContract()));
        PHPUnit_Framework_Assert::assertEquals($pendingEvent->getEventPayload(), $committedEvent->getEventPayload());
        PHPUnit_Framework_Assert::assertEquals($pendingEvent->getBucket(), $committedEvent->getBucket());

        if ($pendingEvent->hasEventMetadataContract() && $committedEvent->hasEventMetadataContract()) {
            PHPUnit_Framework_Assert::assertEquals($pendingEvent->getEventMetadataContract(), $committedEvent->getEventMetadataContract());
        }

        if ($pendingEvent->hasCausationId() && $committedEvent->hasCausationId()) {
            PHPUnit_Framework_Assert::assertEquals($pendingEvent->getCausationId(), $committedEvent->getCausationId());
        }

        if ($pendingEvent->hasCorrelationId() && $committedEvent->hasCorrelationId()) {
            PHPUnit_Framework_Assert::assertEquals($pendingEvent->getCorrelationId(), $committedEvent->getCorrelationId());
        }
    }



} 