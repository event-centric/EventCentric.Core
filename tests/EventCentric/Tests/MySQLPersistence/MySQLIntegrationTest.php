<?php


namespace EventCentric\Tests\MySQLPersistence;


use EventCentric\EventStore\EventStore;
use EventCentric\Fixtures\Order;
use EventCentric\Fixtures\OrderId;
use EventCentric\Fixtures\OrderRepository;
use EventCentric\Fixtures\OrderWasPaidInFull;
use EventCentric\Fixtures\PaymentWasMade;
use EventCentric\Fixtures\ProductId;
use EventCentric\MySQLPersistence\MySQLPersistence;
use EventCentric\Serializer\PhpDomainEventSerializer;
use EventCentric\UnitOfWork\ClassNameBasedAggregateRootReconstituter;
use EventCentric\UnitOfWork\UnitOfWork;

final class MySQLIntegrationTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var MySQLPersistence
     */
    private $mysqlPersistence;

    /**
     * @var OrderRepository
     */
    private $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->mysqlPersistence = new MySQLPersistence(MySQLTestConnector::connect());
        $this->mysqlPersistence->dropSchema();
        $this->mysqlPersistence->createSchema();

        $eventStore = new EventStore($this->mysqlPersistence);
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
        $order->pay(50);
        $this->repository->add($order);

        $retrievedOrder = $this->repository->get($orderId);
        $retrievedOrder->pay(50);
        $changes = $retrievedOrder->getChanges();
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[1]);
    }
}
 