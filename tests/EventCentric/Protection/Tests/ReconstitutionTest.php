<?php
use EventCentric\DomainEvents\Implementations\DomainEventsArray;
use EventCentric\Protection\Tests\Order\Order;
use EventCentric\Protection\Tests\Order\OrderId;
use EventCentric\Protection\Tests\Order\OrderWasPaidInFull;
use EventCentric\Protection\Tests\Order\PaymentWasMade;
use EventCentric\Protection\Tests\Order\ProductId;
use EventCentric\Protection\Tests\Order\ProductWasOrdered;

$test = function() {
    $orderId = OrderId::generate();
    $history = new DomainEventsArray([
        new ProductWasOrdered($orderId, ProductId::generate(), 100),
        new PaymentWasMade($orderId, 50)
    ]);
    $order = Order::reconstituteFrom($history);
    $order->pay(50);
    $changes = $order->getChanges();
    it("should behave as if nothing happened after reconstitution", all([
        count($changes) == 2,
        $changes[0] instanceof PaymentWasMade,
        $changes[1] instanceof OrderWasPaidInFull,
    ]));
};
$test();

