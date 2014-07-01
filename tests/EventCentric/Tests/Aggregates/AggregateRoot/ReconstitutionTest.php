<?php

namespace EventCentric\Tests\Aggregates\AggregateRoot;

use EventCentric\DomainEvents\DomainEventsArray;
use EventCentric\Fixtures\Order;
use EventCentric\Fixtures\OrderId;
use EventCentric\Fixtures\OrderWasPaidInFull;
use EventCentric\Fixtures\PaymentWasMade;
use EventCentric\Fixtures\ProductId;
use EventCentric\Fixtures\ProductWasOrdered;
use PHPUnit_Framework_TestCase;

final class ReconstitutionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_behave_the_same_after_reconstitution()
    {
        $orderId = OrderId::generate();
        $history = new DomainEventsArray([
            new ProductWasOrdered($orderId, ProductId::generate(), 100),
            new PaymentWasMade($orderId, 50)
        ]);
        $order = Order::reconstituteFrom($history);
        $order->pay(50);
        $changes = $order->getChanges();

        $this->assertCount(2, $changes);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[0]);
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[1]);
    }

}
 