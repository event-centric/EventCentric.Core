<?php
use EventCentric\DomainEvents\Implementations\DomainEventsArray;
use EventCentric\Protection\Tests\AggregateRoot\Order\Order;
use EventCentric\Protection\Tests\AggregateRoot\Order\OrderId;
use EventCentric\Protection\Tests\AggregateRoot\Order\OrderWasPaidInFull;
use EventCentric\Protection\Tests\AggregateRoot\Order\PaymentWasMade;
use EventCentric\Protection\Tests\AggregateRoot\Order\ProductId;
use EventCentric\Protection\Tests\AggregateRoot\Order\ProductWasOrdered;

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

