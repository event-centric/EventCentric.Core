<?php

namespace EventCentric\Tests\UnitOfWork;

use EventCentric\Contracts\Contract;
use EventCentric\Tests\Fixtures\Order;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Tests\Fixtures\OrderRepository;
use EventCentric\Tests\Fixtures\OrderWasPaidInFull;
use EventCentric\Tests\Fixtures\ProductId;
use EventCentric\Persistence\Persistence;
use EventCentric\Tests\Persistence\PersistenceProvider;
use EventCentric\UnitOfWork\AggregateRootIsAlreadyBeingTracked;

final class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    use PersistenceProvider;

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Contract
     */
    private $contract;

    protected function setUp()
    {
        parent::setUp();
        $this->orderId = OrderId::generate();
        $this->order = Order::orderProduct($this->orderId, ProductId::generate(), 100);
        $this->contract = Contract::with(Order::class);
    }


    /**
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function retrieved_AggregateRoot_should_behave_the_same_as_the_original_AggregateRoot(Persistence $persistence)
    {
        $unitOfWork = $this->buildUnitOfWork($persistence);
        $repository = new OrderRepository($unitOfWork);


        $this->order->pay(50);
        $repository->add($this->order);
        $unitOfWork->commit();

        $retrievedOrder = $repository->get($this->orderId);
        $retrievedOrder->pay(50);
        $changes = $retrievedOrder->getChanges();
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[1]);
    }

    /**
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function it_should_disallow_tracking_AggregateRoots_twice(Persistence $persistence)
    {
        $unitOfWork = $this->buildUnitOfWork($persistence);
        $unitOfWork->track(Contract::with(Order::class), $this->orderId, $this->order);

        $this->setExpectedException(AggregateRootIsAlreadyBeingTracked::class);
        $unitOfWork->track(Contract::with(Order::class), $this->orderId, $this->order);
    }

    /**
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function it_should_not_persist_events_until_commit_is_called(Persistence $persistence)
    {
        $unitOfWork = $this->buildUnitOfWork($persistence);

        $unitOfWork->track($this->contract, $this->orderId, $this->order);

        $results = $persistence->fetch($this->contract, $this->orderId);
        $this->assertEmpty($results);
    }

    /**
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function it_should_return_tracked_AggregateRoots_when_available(Persistence $persistence)
    {
        $unitOfWork = $this->buildUnitOfWork($persistence);
        $unitOfWork->track($this->contract, $this->orderId, $this->order);
        $unitOfWork->commit();

        // get a new UoW
        $unitOfWork = $this->buildUnitOfWork($persistence);
        $retrievedOrder1 = $unitOfWork->get($this->contract, $this->orderId);
        $retrievedOrder2 = $unitOfWork->get($this->contract, $this->orderId);
        $this->assertSame($retrievedOrder1, $retrievedOrder2);
    }

    /**
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function committing_twice_should_be_idempotent(Persistence $persistence)
    {
        $unitOfWork = $this->buildUnitOfWork($persistence);
        $unitOfWork->track($this->contract, $this->orderId, $this->order);
        $unitOfWork->commit();
        $unitOfWork->commit();
        $this->assertTrue(true, "Testing for idempotency means there's no observable behaviour, so no assertions. This line is here to satisfy PHPUnit strict mode.");
    }
}
 