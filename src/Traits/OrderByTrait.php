<?php

namespace Imponeer\Database\Criteria\Traits;

use Imponeer\Database\Criteria\Enum\Order;

/**
 * That that adds order by methods
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait OrderByTrait
{
    protected Order $order;

    public function __construct()
    {
        $this->order = Order::ASC;
    }

    /**
     * @noinspection MethodShouldBeFinalInspection
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * Sets Order
     *
     * @param string|Order $order Order to set for this criteria element
     *
     * @noinspection MethodShouldBeFinalInspection
     */
    public function setOrder(Order|string $order): self
    {
        $this->order = $order instanceof Order ? $order : Order::from(strtoupper(trim($order)));

        return $this;
    }
}
