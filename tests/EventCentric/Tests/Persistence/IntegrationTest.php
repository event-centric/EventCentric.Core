<?php

namespace EventCentric\Tests\Persistence;

use EventCentric\Tests\Fixtures\Order;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Tests\Fixtures\OrderRepository;
use EventCentric\Tests\Fixtures\OrderWasPaidInFull;
use EventCentric\Tests\Fixtures\ProductId;
use EventCentric\Persistence\Persistence;

final class IntegrationTest extends \PHPUnit_Framework_TestCase
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
}
 