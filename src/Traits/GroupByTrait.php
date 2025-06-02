<?php

namespace Imponeer\Database\Criteria\Traits;

/**
 * Trait that adds information for grouping results
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait GroupByTrait
{
    protected ?string $group = null;

    /**
     * Sets group by
     *
     * @noinspection MethodShouldBeFinalInspection
     * @noinspection PhpDocSignatureInspection
     */
    public function setGroupBy(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Gets a group by value
     *
     * @noinspection MethodShouldBeFinalInspection
     */
    public function getGroupBy(): string
    {
        return $this->group ? ' GROUP BY ' . $this->group : '';
    }

}