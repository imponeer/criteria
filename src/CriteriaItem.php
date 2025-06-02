<?php

namespace Imponeer\Database\Criteria;

use Imponeer\Database\Criteria\Enum\ComparisionOperator;
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
    protected ?string $column = null;
    protected ComparisionOperator $operator;

    /**
     * Data for criteria item
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param string $column
     * @param mixed|null $value
     * @param ComparisionOperator|string $operator
     * @param string|null $prefix
     * @param string|null $function
     */
    public function __construct(
        string $column,
        mixed $value = null,
        ComparisionOperator|string $operator = '=',
        ?string $prefix = null,
        ?string $function = null
    )
    {
        parent::__construct();

        $this->prefix = $prefix;
        $this->function = $function;
        $this->column = $column;
        $this->operator = $operator instanceof ComparisionOperator ? $operator : ComparisionOperator::from(strtoupper(trim($operator)));

        if (is_string($value) && str_starts_with($value, '(')) {
            $this->data[] = $value;
        } else {
            if (is_array($value) && in_array($this->operator->value, [
                    ComparisionOperator::IS_NOT_NULL,
                    ComparisionOperator::IS_NULL,
                    ComparisionOperator::BETWEEN,
                    ComparisionOperator::NOT_BETWEEN,
                ], true)) {
                $this->operator = ComparisionOperator::IS_NULL;
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

    public function getComparisionOperator(): ComparisionOperator
    {
        return $this->operator;
    }

    /**
     * @inheritDoc
     */
    public function render(bool $withBindVariables = false): ?string
    {
        if ($withBindVariables === false) {
            $withBindVariables = is_int($this->column) || // this is also for compatibility
                is_string($this->data) // str_pos is a hack to make old code still run
            ;
        }

        $clause = sprintf("%s%s", $this->prefix ? $this->prefix . "." : '', $this->column);
        if (!empty($this->function)) {
            $clause = sprintf($this->function, $clause);
        }

        switch ($this->operator->value) {
            case ComparisionOperator::IS_NOT_NULL:
            case ComparisionOperator::IS_NULL:
                $clause .= ' ' . $this->operator->value;
                break;
            case ComparisionOperator::BETWEEN:
            case ComparisionOperator::NOT_BETWEEN:
                if ($withBindVariables) {
                    [$fromValue, $toValue] = array_keys($this->data);
                    $clause .= sprintf(" %s %d AND %d", $this->operator->value, (int)$fromValue, (int)$toValue);
                } else {
                    [$fromKey, $toKey] = array_keys($this->data);
                    $clause .= sprintf(" %s :%s AND :%s", $this->operator->value, $fromKey, $toKey);
                }
                break;
            case ComparisionOperator::IN:
            case ComparisionOperator::NOT_IN:
                if (is_string($this->data)) {
                    $clause .= ' ' . $this->operator->value . $this->data;
                    break;
                }
                if (empty($this->data)) {
                    $clause .= ' ' . ($this->operator === ComparisionOperator::IN ? ' IS 0 ' : ' IS 1 ');
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
                    $clause .= sprintf(" %s %s", $this->operator->value, $this->prepareRenderedValue(current($this->data)));
                } else {
                    $clause .= sprintf(" %s :%s", $this->operator->value, key($this->data));
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
     */
    protected function prepareRenderedValue(null|bool|object|string|float|array $value)
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return (string)(int)$value;
        }

        if (is_object($value)) {
            if ((interface_exists(Stringable::class) && ($value instanceof Stringable)) || method_exists($value, '__toString')) {
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
     * @return string
     */
    protected function addSlashes(string $str): string
    {
        return "'" . str_replace(['\\', '\''], ['\\\\', '\\\''], $str) . "'";
    }

    /**
     * @inheritDoc
     */
    public function getBindData(): array
    {
        return $this->data;
    }
}