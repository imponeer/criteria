<?php

namespace Imponeer\Database\Criteria\Traits;

/**
 * Trait for setting fields for sorting results
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait SortByTrait
{

    /**
     * @var    string|null
     */
    protected $sort = null;

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
     * @param string $sort Database field name for sorting
     *
     * @return self
     */
    public function setSort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

}