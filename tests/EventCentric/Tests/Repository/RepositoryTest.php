<?php

namespace EventCentric\Tests\Repository;

use EventCentric\Tests\Fixtures\Order;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Tests\Fixtures\OrderRepository;
use EventCentric\Tests\Fixtures\OrderWasPaidInFull;
use EventCentric\Tests\Fixtures\PaymentWasMade;
use EventCentric\Tests\Fixtures\ProductId;
use EventCentric\Persistence\Persistence;
use EventCentric\Tests\Persistence\PersistenceProvider;
use PHPUnit_Framework_TestCase;

final class RepositoryTest extends PHPUnit_Framework_TestCase
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
        $repository->add($order);

        $retrievedOrder = $repository->get($orderId);

        /** @var $retrievedOrder Order */
        $retrievedOrder->pay(50);
        $retrievedOrder->pay(50);
        $changes = $retrievedOrder->getChanges();

        $this->assertCount(3, $changes);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[0]);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[1]);
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[2]);
        $repository->add($retrievedOrder);
    }
} 