<?php

namespace EventCentric\Protection\Tests;

use EventCentric\Protection\Tests\Order\Order;
use EventCentric\Protection\Tests\Order\OrderId;
use EventCentric\Protection\Tests\Order\OrderWasPaidInFull;
use EventCentric\Protection\Tests\Order\PaymentWasMade;
use EventCentric\Protection\Tests\Order\ProductId;
use EventCentric\Protection\Tests\Order\ProductWasOrdered;

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

