<?php

use EventCentric\Protection\Tests\AggregateRoot\Order\Order;
use EventCentric\Protection\Tests\AggregateRoot\Order\OrderId;
use EventCentric\Protection\Tests\AggregateRoot\Order\ProductId;

$test = function() {
    $order = Order::orderProduct(OrderId::generate(), ProductId::generate(), 100);
    it("should track changes",
        $order->hasChanges()
    );

    $order->clearChanges();
    it("should clear changes",
        !$order->hasChanges());

    $order->pay(50);
    it("should record new changes as well",
        count($order->getChanges()) == 1);
};
$test();
