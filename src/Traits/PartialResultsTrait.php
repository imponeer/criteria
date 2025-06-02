<?php

namespace Imponeer\Database\Criteria\Traits;

/**
 * Defines trait to configure to return partial results
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait PartialResultsTrait
{

    protected int $limit = 0;
    protected int $start = 0;

    /**
     * Gets how many results to return
     *
     * @return    int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Sets how many results to return
     *
     * @param int $limit Limit for results
     *
     * @return self
     */
    public function setLimit(int $limit = 0): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Gets from what record number to return results (counting starts from 0)
     *
     * @return    int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Sets from what record number to return results (counting starts from 0)
     *
     * @param int $start Sets start position
     *
     * @return self
     */
    public function setStart(int $start = 0): self
    {
        $this->start = $start;

        return $this;
    }

}