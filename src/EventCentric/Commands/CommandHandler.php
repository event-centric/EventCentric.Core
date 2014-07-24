<?php


namespace EventCentric\Commands;

/**
 * Executes a Command
 */
interface CommandHandler 
{
    public function handle(Command $command);
} 