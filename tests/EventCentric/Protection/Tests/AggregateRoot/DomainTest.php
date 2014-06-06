<?php

namespace EventCentric\Protection\Tests\AggregateRoot;

use EventCentric\Protection\Tests\AggregateRoot\Order\Order;
use EventCentric\Protection\Tests\AggregateRoot\Order\OrderId;
use EventCentric\Protection\Tests\AggregateRoot\Order\OrderWasPaidInFull;
use EventCentric\Protection\Tests\AggregateRoot\Order\PaymentWasMade;
use EventCentric\Protection\Tests\AggregateRoot\Order\ProductId;
use EventCentric\Protection\Tests\AggregateRoot\Order\ProductWasOrdered;

$test = function(){
    $orderId = OrderId::generate();
    $order = Order::orderProduct($orderId, ProductId::generate(), 100);
    $order->pay(50);
    $order->pay(50);
    $changes = $order->getChanges();

    it("should protect invariants", all([
        count($changes) == 4,
        $changes[0] instanceof ProductWasOrdered,
        $changes[1] instanceof PaymentWasMade,
        $changes[2] instanceof PaymentWasMade,
        $changes[3] instanceof OrderWasPaidInFull,
    ]));
};

$test();

