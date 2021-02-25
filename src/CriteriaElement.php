<?php

namespace Imponeer\Database\Criteria;

use Imponeer\Database\Criteria\Exceptions\UnsupportedOrderException;
use Imponeer\Database\Criteria\Traits\GroupByTrait;
use Imponeer\Database\Criteria\Traits\OrderByTrait;
use Imponeer\Database\Criteria\Traits\PartialResultsTrait;
use Imponeer\Database\Criteria\Traits\RenderingTrait;
use Imponeer\Database\Criteria\Traits\SortByTrait;

/**
 * Defines base criteria element
 *
 * @package Imponeer\Database\Criteria
 */
abstract class CriteriaElement
{
    use GroupByTrait, OrderByTrait, PartialResultsTrait, RenderingTrait, SortByTrait;

    /**
     * Gets data for rendered query for binding
     *
     * @return array
     */
    abstract public function getBindData(): array;

    /**
     * For compatibility. Probably will be removed in feature
     * Gets variable value
     *
     * @param string $name Variable name
     *
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'order':
                return (string)$this->getOrder();
            case 'sort':
                return $this->getSort();
            case 'limit':
                return $this->getLimit();
            case 'start':
                return $this->getStart();
            case 'groupBy':
                /** @noinspection SpellCheckingInspection */
            case 'groupby':
                return $this->getGroupBy();
        }
    }

    /**
     * For compatibility. Probably will be removed in feature
     * Sets variable
     *
     * @param string $name Variable name
     * @param mixed $value Variable value
     *
     * @return mixed
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'order':
                $this->setOrder($value);
                break;
            case 'sort':
                $this->setSort($value);
                break;
            case 'limit':
                $this->setLimit($value);
                break;
            case 'start':
                $this->setStart($value);
                break;
            case 'groupBy':
                /** @noinspection SpellCheckingInspection */
            case 'groupby':
                $this->setGroupBy($value);
                break;
        }
    }

    /**
     * For compatibility. Probably will be removed in feature
     * Checks if variable value is set
     *
     * @param string $name Variable name
     *
     * @return mixed
     */
    public function __isset($name)
    {
        switch ($name) {
            case 'order':
                return $this->getOrder() !== null;
            case 'sort':
                return !empty($this->getSort());
            case 'limit':
                return !empty($this->getLimit());
            case 'start':
                return !empty($this->getStart());
            case 'groupBy':
                /** @noinspection SpellCheckingInspection */
            case 'groupby':
                return !empty($this->getGroupBy());
            default:
                return false;
        }
    }
}