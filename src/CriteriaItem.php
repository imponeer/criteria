<?php

namespace Imponeer\Database\Criteria;

use Imponeer\Database\Criteria\Enum\ComparisonOperator;
use Imponeer\Database\Criteria\Enum\Order;
use Imponeer\Database\Criteria\Helpers\UniqueBindParam;
use JsonException;
use PHPUnit\Framework\Constraint\Operator;
use Stringable;

/**
 * Defines single Criteria item
 *
 * @package Imponeer\Database\Criteria
 */
class CriteriaItem extends CriteriaElement
{
    protected ?string $prefix = null;
    protected ?string $function = null;
    protected null|string|int $column = null;
    protected ComparisonOperator $operator;

    /**
     * Data for criteria item
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param string|int $column
     * @param mixed|null $value
     * @param ComparisonOperator|string $operator
     * @param string|null $prefix
     * @param string|null $function
     */
    public function __construct(
        string|int $column,
        mixed $value = null,
        ComparisonOperator|string $operator = '=',
        ?string $prefix = null,
        ?string $function = null
    ) {
        parent::__construct();

        $this->prefix = $prefix;
        $this->function = $function;
        $this->column = $column;
        $this->operator = ComparisonOperator::resolve($operator);

        if (is_string($value) && str_starts_with($value, '(')) {
            $this->data[] = $value;
        } else {
            if ($this->isEmptyArrayButNotNullComparison($value)) {
                $this->operator = ComparisonOperator::IS_NULL;
            }

            if (is_array($value)) {
                $this->data = [];
                foreach ($value as $val) {
                    $this->data[UniqueBindParam::generate($this->column)] = $val;
                }
            } else {
                $this->data = [
                    UniqueBindParam::generate($this->column) => $value
                ];
            }
        }
    }

    /**
     * @noinspection MethodShouldBeFinalInspection
     */
    public function getComparisonOperator(): ComparisonOperator
    {
        return $this->operator;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     *
     * @noinspection MethodShouldBeFinalInspection
     */
    public function render(bool $withBindVariables = false): ?string
    {
        if ($withBindVariables === false) {
            $withBindVariables = is_int($this->column); // this is also for compatibility
        }

        $clause = sprintf("%s%s", $this->prefix ? $this->prefix . "." : '', $this->column);
        if (!empty($this->function)) {
            $clause = sprintf($this->function, $clause);
        }

        switch ($this->operator->value) {
            case ComparisonOperator::IS_NOT_NULL:
            case ComparisonOperator::IS_NULL:
                $clause .= ' ' . $this->operator->value;
                break;
            case ComparisonOperator::BETWEEN:
            case ComparisonOperator::NOT_BETWEEN:
                if ($withBindVariables) {
                    [$fromValue, $toValue] = array_keys($this->data);
                    $clause .= sprintf(" %s %d AND %d", $this->operator->value, (int)$fromValue, (int)$toValue);
                } else {
                    [$fromKey, $toKey] = array_keys($this->data);
                    $clause .= sprintf(" %s :%s AND :%s", $this->operator->value, $fromKey, $toKey);
                }
                break;
            case ComparisonOperator::IN:
            case ComparisonOperator::NOT_IN:
                if (empty($this->data)) {
                    $clause .= ' ' . ($this->operator === ComparisonOperator::IN ? ' IS 0 ' : ' IS 1 ');
                    break;
                }
                $clause .= ' ' . $this->operator->value . '(';
                if ($withBindVariables) {
                    foreach (array_values($this->data) as $i => $value) {
                        if ($i !== 0) {
                            $clause .= ', ';
                        }
                        $clause .= $this->prepareRenderedValue($value);
                    }
                } else {
                    foreach (array_keys($this->data) as $i => $key) {
                        if ($i !== 0) {
                            $clause .= ', ';
                        }
                        $clause .= ':' . $key;
                    }
                }
                $clause .= ')';
                break;
            default:
                if ($withBindVariables) {
                    $clause .= sprintf(
                        " %s %s",
                        $this->operator->value,
                        $this->prepareRenderedValue(current($this->data))
                    );
                } else {
                    $clause .= sprintf(
                        " %s :%s",
                        $this->operator->value,
                        key($this->data)
                    );
                }
        }

        return $clause;
    }

    /**
     * Makes value as query part
     *
     * @param null|bool|object|string|float $value Value to make as query part
     *
     * @return string
     *
     * @throws JsonException
     *
     * @noinspection MethodVisibilityInspection
     * @noinspection MethodShouldBeFinalInspection
     */
    protected function prepareRenderedValue(null|bool|object|string|float|array $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return (string)(int)$value;
        }

        if (is_object($value)) {
            if ($value instanceof Stringable) {
                return $this->addSlashes((string)$value);
            }

            return $this->addSlashes(serialize($value));
        }

        if (is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        if ($value === '') {
            return "''";
        }

        if ((!str_starts_with($value, '`')) && (!str_ends_with($value, '`'))) {
            return $this->addSlashes($value);
        }

        return $value;
    }

    /**
     * Add Mysql content slashes to string
     *
     * @param string $str String where to add slashes
     *
     * @noinspection MethodVisibilityInspection
     * @noinspection MethodShouldBeFinalInspection
     */
    protected function addSlashes(string $str): string
    {
        return "'" . str_replace(['\\', '\''], ['\\\\', '\\\''], $str) . "'";
    }

    /**
     * @inheritDoc
     *
     * @noinspection MethodShouldBeFinalInspection
     */
    public function getBindData(): array
    {
        return $this->data;
    }

    private function isEmptyArrayButNotNullComparison(mixed $value): bool
    {
        return is_array($value) && empty($value) && in_array($this->operator, [
                ComparisonOperator::IS_NOT_NULL,
                ComparisonOperator::IS_NULL,
                ComparisonOperator::BETWEEN,
                ComparisonOperator::NOT_BETWEEN,
            ], true);
    }
}
