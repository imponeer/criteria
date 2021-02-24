<?php

namespace Imponeer\Database\Criteria\Traits;

/**
 * Trait that adds information for grouping results
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait GroupByTrait
{
    /**
     * @var null|string
     */
    protected $group = null;

    /**
     * Sets group by
     *
     * @param string $group
     *
     * @return self
     */
    public function setGroupBy($group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Gets group by value
     *
     * @return    string
     */
    public function getGroupBy(): string
    {
        return $this->group ? ' GROUP BY ' . $this->group : '';
    }

}