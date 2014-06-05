<?php

namespace EventCentric\Example\Order;

use EventCentric\DomainEvents\Implementations\DomainEventsArray;

require_once __DIR__.'/../vendor/autoload.php';

$order = Order::orderProduct(OrderId::generate(), ProductId::generate(), new Money(100, 'EUR'));

assert($order->hasChanges());
$changes = $order->getChanges();
assert(count($changes) == 1);
assert($changes[0] instanceof ProductWasOrdered);
$order->clearChanges();
assert(!$order->hasChanges());

$order->pay(new Money(100, 'EUR'));
assert($order->hasChanges());
$changes2 = $order->getChanges();
assert(count($changes2) == 1);
assert($changes2[0] instanceof OrderWasPaid);


$orderId = OrderId::generate();
$history = new DomainEventsArray([
    new ProductWasOrdered($orderId, ProductId::generate(), new Money(100, 'EUR')),
    new OrderWasPaid($orderId, ProductId::generate(), new Money(100, 'EUR'))
]);
$order2 = Order::reconstituteFrom($history);