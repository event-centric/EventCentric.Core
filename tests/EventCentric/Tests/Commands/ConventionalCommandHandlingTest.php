<?php

namespace EventCentric\Commands;

use PHPUnit_Framework_TestCase;

final class ConventionalCommandHandlingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_delegate_a_Command()
    {
        $command = new FakeCommand();
        $commandHandler = new SpyingConventionalCommandHandler();

        $commandHandler->handle($command);

        $this->assertTrue(
            $commandHandler->hasHandled($command)
        );
    }

    /**
     * @test
     */
    public function it_should_throw_when_handle_implementation_is_missing()
    {
        $command = new FakeCommand();
        $commandHandler = new ConventionalCommandHandlerWithoutHandleImplementation();

        $this->setExpectedException(CommandCouldNotBeHandled::class);

        $commandHandler->handle($command);
    }
}


final class FakeCommand implements Command {}

final class SpyingConventionalCommandHandler implements CommandHandler
{
    use ConventionalCommandHandling;

    private $handled;

    protected function handleFakeCommand(FakeCommand $command)
    {
        $this->handled = $command;
    }

    public function hasHandled(Command $command)
    {
        return $this->handled === $command;
    }
}

final class ConventionalCommandHandlerWithoutHandleImplementation implements CommandHandler
{
    use ConventionalCommandHandling;
}