<?php

namespace EventCentric\Commands;

use Exception;
use Verraes\ClassFunctions\ClassFunctions;

final class CommandCouldNotBeHandled extends Exception
{
    public static function forCommand(Command $command, CommandHandler $commandHandler, $message = "")
    {
        return new CommandCouldNotBeHandled(
            sprintf("The command %s could not be handled by %s\n%s", ClassFunctions::fqcn($command), ClassFunctions::fqcn($commandHandler), $message)
        );
    }
}
