<?php

namespace EventCentric\Aggregates\Tests\AggregateRoot;

use EventCentric\Fixtures\Order;
use EventCentric\Fixtures\OrderId;
use EventCentric\Fixtures\OrderWasPaidInFull;
use EventCentric\Fixtures\PaymentWasMade;
use EventCentric\Fixtures\ProductId;
use EventCentric\Fixtures\ProductWasOrdered;
use PHPUnit_Framework_TestCase;

final class DomainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_protect_invariants()
    {
        $orderId = OrderId::generate();
        $order = Order::orderProduct($orderId, ProductId::generate(), 100);
        $order->pay(50);
        $order->pay(50);
        $changes = $order->getChanges();

        $this->assertCount(4, $changes);
        $this->assertInstanceOf(ProductWasOrdered::class, $changes[0]);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[1]);
        $this->assertInstanceOf(PaymentWasMade::class, $changes[2]);
        $this->assertInstanceOf(OrderWasPaidInFull::class, $changes[3]);
    }
}
 