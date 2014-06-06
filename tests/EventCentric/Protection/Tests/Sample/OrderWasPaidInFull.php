<?php

namespace EventCentric\Protection\Tests\Sample;

use EventCentric\DomainEvents\DomainEvent;

final class OrderWasPaidInFull implements DomainEvent
{
    /**
     * @var OrderId
     */
    private $orderId;

    public function __construct(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

} 