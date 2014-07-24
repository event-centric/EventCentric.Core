<?php

namespace EventCentric\Commands;

/**
 * Delivers a Command to the right CommandHandler
 */
interface CommandDispatcher 
{
    public function dispatch(Command $command);
}