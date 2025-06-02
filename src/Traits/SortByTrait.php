<?php

namespace Imponeer\Database\Criteria\Traits;

use Imponeer\Database\Criteria\CriteriaElement;

/**
 * Trait for setting fields for sorting results
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait SortByTrait
{

    protected ?string $sort = null;

    /**
     * Gets sort field
     *
     * @return    string
     */
    public function getSort(): string
    {
        return (string)$this->sort;
    }

    /**
     * Sets sort field
     *
     * @param string|null $sort Database field name for sorting
     *
     * @return SortByTrait|CriteriaElement
     */
    public function setSort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

}