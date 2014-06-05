<?php

namespace EventCentric\Example\Order;

use EventCentric\DomainEvents\DomainEvent;

final class ProductWasOrdered implements DomainEvent
{
    /**
     * @var ProductId
     */
    private $productId;
    /**
     * @var OrderId
     */
    private $orderId;
    /**
     * @var Money
     */
    private $price;

    public function __construct(OrderId $orderId, ProductId $productId, Money $price)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->price = $price;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

} 