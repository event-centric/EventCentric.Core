<?php

namespace EventCentric\Tests\Fixtures;

use EventCentric\DomainEvents\DomainEvent;

final class PaymentWasMade implements DomainEvent
{
    /** @var OrderId */
    private $orderId;
    /** @var int */
    private $amount;

    public function __construct(OrderId $orderId, $amount)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getPaidAmount()
    {
        return $this->amount;
    }
}