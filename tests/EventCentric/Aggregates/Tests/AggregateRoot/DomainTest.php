<?php

namespace EventCentric\Aggregates\Tests\AggregateRoot;

use EventCentric\Aggregates\Tests\Sample\Order;
use EventCentric\Aggregates\Tests\Sample\OrderId;
use EventCentric\Aggregates\Tests\Sample\OrderWasPaidInFull;
use EventCentric\Aggregates\Tests\Sample\PaymentWasMade;
use EventCentric\Aggregates\Tests\Sample\ProductId;
use EventCentric\Aggregates\Tests\Sample\ProductWasOrdered;
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
 