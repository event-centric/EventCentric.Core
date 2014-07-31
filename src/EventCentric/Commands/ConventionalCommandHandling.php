<?php

namespace EventCentric\Commands;

use Verraes\ClassFunctions\ClassFunctions;

/**
 * Implements CommandHandler through naming convention
 */
trait ConventionalCommandHandling
{
    public function handle(Command $command)
    {
        $short = ClassFunctions::short($command);
        $method = 'handle' . $short;

        if(!is_callable([$this, $method])) {
            $message = <<<MSG
The CommandHandler needs the following code to operate:
protected function $method($short \$command)
{
}
MSG;
            throw CommandCouldNotBeHandled::forCommand($command, $this, $message);
        }

        $this->{$method}($command);
    }
}
