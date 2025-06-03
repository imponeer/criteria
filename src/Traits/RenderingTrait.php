<?php

namespace Imponeer\Database\Criteria\Traits;

/**
 * Trait fort criteria rendering
 *
 * @package Imponeer\Database\Criteria\Traits
 */
trait RenderingTrait
{
    /**
     * Renders as 'where' SQL string
     *
     * @param bool $withBinds Render with bind variables
     *
     * @noinspection MethodShouldBeFinalInspection
     */
    public function renderWhere(bool $withBinds = false): string
    {
        $cond = $this->render($withBinds);
        return $cond ? "WHERE $cond" : '';
    }

    /**
     * Render the criteria string
     *
     * @param bool $withBindVariables Render with bind variables
     */
    abstract public function render(bool $withBindVariables = false): ?string;
}
