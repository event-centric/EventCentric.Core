<?php

namespace EventCentric\Commands;

use Exception;

final class NoSuitableCommandHandlerWasFound extends Exception
{
    /**
     * @param Command $command
     * @return NoSuitableCommandHandlerWasFound
     */
    public static function forCommand(Command $command)
    {
        return new NoSuitableCommandHandlerWasFound(
            sprintf("No registered CommandHandler was found for %s", get_class($command))
        );
    }
}
