<?php

namespace EventCentric\Persistence;

use Exception;

final class OptimisticConcurrencyFailed extends Exception
{
    public static function revisionDoNotMatch($expectedStreamRevision, $actualStreamRevision)
    {
        $message = sprintf("Expected streamVersion = %d, got %d", $expectedStreamRevision, $actualStreamRevision);
        return new OptimisticConcurrencyFailed($message);
    }
} 