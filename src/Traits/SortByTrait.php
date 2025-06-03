<?php

namespace Imponeer\Database\Criteria\Traits;

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
     * @noinspection MethodShouldBeFinalInspection
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
     * @noinspection MethodShouldBeFinalInspection
     */
    public function setSort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}
