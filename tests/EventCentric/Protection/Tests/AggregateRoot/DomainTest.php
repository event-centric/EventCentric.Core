<?php

namespace EventCentric\Protection\Tests\AggregateRoot;

use EventCentric\Protection\Tests\Sample\Order;
use EventCentric\Protection\Tests\Sample\OrderId;
use EventCentric\Protection\Tests\Sample\OrderWasPaidInFull;
use EventCentric\Protection\Tests\Sample\PaymentWasMade;
use EventCentric\Protection\Tests\Sample\ProductId;
use EventCentric\Protection\Tests\Sample\ProductWasOrdered;
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
 