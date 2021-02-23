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

    /**
     * Sort order
     *
     * @var    Order
     */
    protected $order;

    /**
     * OrderByTrait constructor.
     */
    public function __construct()
    {
        $this->order = Order::ASC();
    }

    /**
     * Gets order
     *
     * @return    Order
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
     * @return self
     */
    public function setOrder($order): self
    {
        if (!($order instanceof Order)) {
            Order::assertValidValue($order);
            $this->order = Order::from($order);
        } else {
            $this->order = $order;
        }

        return $this;
    }

}