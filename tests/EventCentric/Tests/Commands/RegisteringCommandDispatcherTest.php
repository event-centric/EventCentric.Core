<?php

namespace EventCentric\Tests\Commands;

use EventCentric\Commands\Command;
use EventCentric\Commands\CommandHandler;
use EventCentric\Commands\RegisteringCommandDispatcher;
use EventCentric\Commands\NoSuitableCommandHandlerWasFound;

final class RegisteringCommandDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisteringCommandDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->dispatcher = new RegisteringCommandDispatcher();
    }

    /**
     * @test
     */
    public function it_should_dispatch_only_to_CommandHandler_registered_for_that_Command()
    {
        $correctCommandHandler = new SpyingCommandHandler();
        $otherCommandHandler = new SpyingCommandHandler();
        $command = new FakeCommand();

        $this->dispatcher->register(FakeCommand::class, $correctCommandHandler);
        $this->dispatcher->register(OtherFakeCommand::class, $otherCommandHandler);
        $this->dispatcher->dispatch($command);

        $this->assertTrue($correctCommandHandler->hasHandled($command));
        $this->assertFalse($otherCommandHandler->hasHandled($command));
    }

    /**
     * @test
     */
    public function it_should_throw_for_missing_CommandHandler()
    {
        $command = new FakeCommand();
        $this->setExpectedException(NoSuitableCommandHandlerWasFound::class, "No registered CommandHandler was found for ".FakeCommand::class);
        $this->dispatcher->dispatch($command);
    }
}

final class FakeCommand implements Command {}
final class OtherFakeCommand implements Command {}

final class SpyingCommandHandler implements CommandHandler
{
    private $handled;

    public function handle(Command $command)
    {
        $this->handled = $command;
    }

    public function hasHandled(Command $command)
    {
        return $this->handled === $command;
    }
}