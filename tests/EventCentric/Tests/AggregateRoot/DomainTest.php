<?php

namespace EventCentric\Tests\AggregateRoot;

use EventCentric\Tests\Fixtures\Order;
use EventCentric\Tests\Fixtures\OrderId;
use EventCentric\Tests\Fixtures\OrderWasPaidInFull;
use EventCentric\Tests\Fixtures\PaymentWasMade;
use EventCentric\Tests\Fixtures\ProductId;
use EventCentric\Tests\Fixtures\ProductWasOrdered;
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
 