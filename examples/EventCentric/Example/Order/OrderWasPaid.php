<?php

namespace EventCentric\Example\Order;

use EventCentric\DomainEvents\DomainEvent;

final class OrderWasPaid implements DomainEvent
{
    /**
     * @var OrderId
     */
    private $orderId;
    /**
     * @var Money
     */
    private $amount;

    function __construct($orderId, $amount)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    /**
     * @return \EventCentric\Example\Order\Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return \EventCentric\Example\Order\OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
} 