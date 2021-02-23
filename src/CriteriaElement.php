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
}