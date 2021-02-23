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
     * Adds Xoops like calls compatibility
     *
     * @param string $name Function name
     * @param array $arguments Arguments array
     *
     * @return GroupByTrait|string
     */
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'setGroupby':
                return $this->setGroupBy($arguments[0]);
            case 'getGroupby':
                return $this->getGroupBy();
        }
    }

    /**
     * @param string $group
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