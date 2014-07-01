<?php

namespace EventCentric\Tests\Repository;

use EventCentric\EventStore;
use EventCentric\Fixtures\Order;
use EventCentric\Fixtures\OrderId;
use EventCentric\Fixtures\OrderRepository;
use EventCentric\Fixtures\OrderWasPaidInFull;
use EventCentric\Fixtures\PaymentWasMade;
use EventCentric\Fixtures\ProductId;
use EventCentric\Fixtures\ProductWasOrdered;
use EventCentric\Serializer\PhpDomainEventSerializer;
use PHPUnit_Framework_TestCase;

final class RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OrderRepository
     */
    private $repository;

    /**
     * @var EventStore
     */
    private $eventStore;

    private $eventSerializer;

    protected function setUp()
    {
        parent::setUp();
        $this->eventStore = new EventStore;
        $this->eventSerializer = new PhpDomainEventSerializer();
        $this->repository = new OrderRepository($this->eventStore, $this->eventSerializer);
    }


    /**
     * @test
     */
    public function retrieved_order_should_behave_the_same_as_the_original_order()
    {
        $orderId = OrderId::generate();
        $order = Order::orderProduct($orderId, ProductId::generate(), 100);
        $this->repository->add($order);

        $retrievedOrder = $this->repository->get($orderId);

        /** @var $retrievedOrder Order */
        $retrievedOrder->pay(50);
        $retrievedOrder->pay(50);
        $changes = $order->getChanges();

        $this->assertCount(4, $changes);
        $this->assertInstanceOf(ProductWasOrdered::class, $changes[0]);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[1]);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[2]);
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[3]);

    }
} 