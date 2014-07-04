<?php

namespace EventCentric\Tests\Persistence;

use EventCentric\Fixtures\Order;
use EventCentric\Fixtures\OrderId;
use EventCentric\Fixtures\OrderRepository;
use EventCentric\Fixtures\OrderWasPaidInFull;
use EventCentric\Fixtures\ProductId;
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
 