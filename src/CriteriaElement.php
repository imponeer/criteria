<?php

namespace Imponeer\Database\Criteria;

use Exception;
use Imponeer\Database\Criteria\Traits\GroupByTrait;
use Imponeer\Database\Criteria\Traits\OrderByTrait;
use Imponeer\Database\Criteria\Traits\PartialResultsTrait;
use Imponeer\Database\Criteria\Traits\RenderingTrait;
use Imponeer\Database\Criteria\Traits\SortByTrait;
use JsonSerializable;
use Stringable;
use ValueError;

/**
 * Defines base criteria element
 *
 * @package Imponeer\Database\Criteria
 */
abstract class CriteriaElement implements Stringable
{
    use GroupByTrait;
    use OrderByTrait;
    use PartialResultsTrait;
    use RenderingTrait;
    use SortByTrait;

    /**
     * Gets data for rendered query for binding
     *
     * @return array
     */
    abstract public function getBindData(): array;

    /**
     * For compatibility. Probably will be removed in feature
     * Gets variable value
     */
    public function __get(string $name): string|int
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
     * For compatibility. Probably will be removed in the feature
     * Sets variable
     */
    public function __set(string $name, mixed $value): void
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
     * Checks if a variable value is set
     */
    public function __isset(string $name): bool
    {
        return match ($name) {
            'order' => true,
            'sort' => !empty($this->getSort()),
            'limit' => !empty($this->getLimit()),
            'start' => !empty($this->getStart()),
            'groupBy', 'groupby' => !empty($this->getGroupBy()),
            default => false,
        };
    }

    /**
     * @throws Exception
     */
    public function __toString(): string
    {
        return $this->render(true);
    }
}
