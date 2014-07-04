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
     * @test
     * @dataProvider providePersistence
     * @param Persistence $persistence
     */
    public function retrieved_order_should_behave_the_same_as_the_original_order(Persistence $persistence)
    {
        $unitOfWork = $this->buildUnitOfWork($persistence);
        $repository = new OrderRepository($unitOfWork);

        $orderId = OrderId::generate();
        $order = Order::orderProduct($orderId, ProductId::generate(), 100);
        $order->pay(50);
        $repository->add($order);

        $retrievedOrder = $repository->get($orderId);
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
        $orderId = OrderId::generate();
        $order = Order::orderProduct($orderId, ProductId::generate(), 100);
        $unitOfWork->track(Contract::with(Order::class), $orderId, $order);

        $this->setExpectedException(AggregateRootIsAlreadyBeingTracked::class);
        $unitOfWork->track(Contract::with(Order::class), $orderId, $order);
    }
}
 