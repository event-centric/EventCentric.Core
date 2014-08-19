<?php

namespace EventCentric\Commands;

/**
 * Delivers a Command to the right CommandHandler
 */
interface CommandDispatcher
{
    /**
     * @param Command $command
     * @return void
     */
    public function dispatch(Command $command);
}
