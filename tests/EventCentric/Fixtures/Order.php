<?php

namespace EventCentric\Fixtures;

use EventCentric\Aggregates\AggregateRoot\AggregateRootEntity;

final class Order extends AggregateRootEntity
{
    /** @var OrderId */
    private $orderId;
    /** @var int */
    private $price;
    /** @var int */
    private $totalPaidAmount;

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param OrderId $orderId
     * @param ProductId $productId
     * @param int $price
     * @return Order
     */
    public static function orderProduct(OrderId $orderId, ProductId $productId, $price)
    {
        return (new Order)->recordThat(
            new ProductWasOrdered($orderId, $productId, $price)
        );
    }

    public function pay($amount)
    {
        $this->recordThat(
            new PaymentWasMade($this->orderId, $amount)
        );

        if($this->orderIsPaidInFull()) {
            $this->recordThat(
                new OrderWasPaidInFull($this->orderId, $amount)
            );
        }
    }

    protected function whenProductWasOrdered(ProductWasOrdered $event)
    {
        $this->orderId = $event->getOrderId();
        $this->price = $event->getPrice();
        $this->totalPaidAmount = 0;
    }

    protected function whenPaymentWasMade(PaymentWasMade $event)
    {
        $this->totalPaidAmount += $event->getPaidAmount();
    }

    private function orderIsPaidInFull()
    {
        return $this->totalPaidAmount >= $this->price;
    }

}