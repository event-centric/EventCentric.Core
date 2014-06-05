<?php

namespace EventCentric\Example\Order;

use EventCentric\Identity\Uuid\UuidIdentity;
use EventCentric\Protection\AggregateRoot\AggregateRootEntity;
use EventCentric\Protection\AggregateRoot\EventSourcing;
use EventCentric\Protection\AggregateRoot\Reconstitution;
use EventCentric\When\ConventionBased\ConventionBasedWhen;

final class Order extends AggregateRootEntity
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @param OrderId $orderId
     * @param ProductId $productId
     * @param Money $price
     * @return Order
     */
    public static function orderProduct(OrderId $orderId, ProductId $productId, Money $price)
    {
        return (new Order)->recordThat(
            new ProductWasOrdered($orderId, $productId, $price)
        );
    }

    public function pay(Money $amount)
    {
        $this->recordThat(
            new OrderWasPaid($this->orderId, $amount)
        );
    }

    protected function whenProductWasOrdered(ProductWasOrdered $event)
    {
        $this->orderId = $event->getOrderId();
    }

}