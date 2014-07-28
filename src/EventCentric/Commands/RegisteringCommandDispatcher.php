<?php

namespace EventCentric\Commands;

/**
 * Registering CommandHandlers works fine for small systems. With lots of CommandHandlers, you'll want a more lazy approach.
 */
final class RegisteringCommandDispatcher implements CommandDispatcher
{
    /**
     * @var CommandHandler[]
     */
    private $commandHandlers = [];

    public function register($commandFqcn, CommandHandler $commandHandler)
    {
        $this->commandHandlers[$commandFqcn] = $commandHandler;
    }

    public function dispatch(Command $command)
    {
        $commandFqcn = get_class($command);
        if(!array_key_exists($commandFqcn, $this->commandHandlers)){
            throw NoSuitableCommandHandlerWasFound::forCommand($command);
        }
        $this->commandHandlers[$commandFqcn]->handle($command);
    }
}