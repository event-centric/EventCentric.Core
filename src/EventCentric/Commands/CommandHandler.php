<?php

namespace EventCentric\Commands;

/**
 * Executes a Command
 * Typically, a CommandHandler creates an AggregateRoot, or fetches an AggregateRoot from a Repository. It calls an
 * operation on the AggregateRoot. If the AggregateRoot is new, it will add() it to the Repository. Transaction
 * management is not part of the CommandHandler. (Opinions vary though).
 * ConventionalCommandHandling can be used for the implementations.
 */
interface CommandHandler 
{
    /**
     * @param Command $command
     * @return void
     */
    public function handle(Command $command);
}
