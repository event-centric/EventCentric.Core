<?php

namespace EventCentric\Tests\Repository;

use EventCentric\EventStore\EventStore;
use EventCentric\Fixtures\Order;
use EventCentric\Fixtures\OrderId;
use EventCentric\Fixtures\OrderRepository;
use EventCentric\Fixtures\OrderWasPaidInFull;
use EventCentric\Fixtures\PaymentWasMade;
use EventCentric\Fixtures\ProductId;
use EventCentric\Persistence\InMemoryPersistence;
use EventCentric\Serializer\PhpDomainEventSerializer;
use EventCentric\UnitOfWork\ClassNameBasedAggregateRootReconstituter;
use EventCentric\UnitOfWork\UnitOfWork;
use PHPUnit_Framework_TestCase;

final class RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OrderRepository
     */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $eventStore = new EventStore(new InMemoryPersistence());
        $eventSerializer = new PhpDomainEventSerializer();
        $aggregateRootReconstituter = new ClassNameBasedAggregateRootReconstituter();
        $unitOfWork = new UnitOfWork($eventStore, $eventSerializer, $aggregateRootReconstituter);

        $this->repository = new OrderRepository($unitOfWork);
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
        $changes = $retrievedOrder->getChanges();

        $this->assertCount(3, $changes);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[0]);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[1]);
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[2]);

    }
} 