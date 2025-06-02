<?php

namespace Imponeer\Database\Criteria;

use Imponeer\Database\Criteria\Traits\GroupByTrait;
use Imponeer\Database\Criteria\Traits\OrderByTrait;
use Imponeer\Database\Criteria\Traits\PartialResultsTrait;
use Imponeer\Database\Criteria\Traits\RenderingTrait;
use Imponeer\Database\Criteria\Traits\SortByTrait;
use ValueError;

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
    public function __get(string $name): mixed
    {
        return match ($name) {
            'order' => $this->getOrder()->value,
            'sort' => $this->getSort(),
            'limit' => $this->getLimit(),
            'start' => $this->getStart(),
            'groupBy', 'groupby' => $this->getGroupBy(),
            default => throw new ValueError('Unknown property: ' . $name),
        };
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
    public function __set(string $name, mixed $value)
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
        return match ($name) {
            'order' => $this->getOrder() !== null,
            'sort' => !empty($this->getSort()),
            'limit' => !empty($this->getLimit()),
            'start' => !empty($this->getStart()),
            'groupBy', 'groupby' => !empty($this->getGroupBy()),
            default => false,
        };
    }
}