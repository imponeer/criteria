<?php

namespace Imponeer\Database\Criteria;

use Imponeer\Database\Criteria\Enum\ComparisionOperator;
use Imponeer\Database\Criteria\Helpers\UniqueBindParam;

/**
 * Defines single Criteria item
 *
 * @package Imponeer\Database\Criteria
 */
class CriteriaItem extends CriteriaElement
{

    /**
     * @var    string|null
     */
    protected $prefix = null;

    /**
     * @var string|null
     */
    protected $function = null;

    /**
     * @var string|null
     */
    protected $column = null;

    /**
     * @var ComparisionOperator
     */
    protected $operator;

    /**
     * Data for criteria item
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     *
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @param string $prefix
     * @param string $function
     */
    public function __construct($column, $value = null, $operator = '=', ?string $prefix = null, ?string $function = null)
    {
        parent::__construct();

        $this->prefix = $prefix;
        $this->function = $function;
        $this->column = $column;

        if ($operator instanceof ComparisionOperator) {
            $this->operator = $operator;
        } else {
            ComparisionOperator::assertValidValue($operator);
            $this->operator = ComparisionOperator::from($operator);
        }

        if (is_string($value) && strpos($value, '(') === 0) {
            $this->data = $value;
        } else {
            if (is_array($value) && in_array($this->operator->getValue(), [
                    ComparisionOperator::IS_NOT_NULL,
                    ComparisionOperator::IS_NULL,
                    ComparisionOperator::BETWEEN,
                    ComparisionOperator::NOT_BETWEEN,
                ], true)) {
                $this->operator = ComparisionOperator::IS_NULL();
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
     * @inheritDoc
     */
    public function render(bool $withBindVariables = false): ?string
    {
        if ($withBindVariables === false) {
            $withBindVariables = is_int($this->column) || // this is also for compatibility
                is_string($this->data) // strpos is a hack to make old code still run
            ;
        }

        $clause = sprintf("%s%s", $this->prefix ? $this->prefix . "." : '', $this->column);
        if (!empty($this->function)) {
            $clause = sprintf($this->function, $clause);
        }

        switch ($this->operator->getValue()) {
            case ComparisionOperator::IS_NOT_NULL:
            case ComparisionOperator::IS_NULL:
                $clause .= ' ' . $this->operator;
                break;
            case ComparisionOperator::BETWEEN:
            case ComparisionOperator::NOT_BETWEEN:
                if ($withBindVariables) {
                    [$fromValue, $toValue] = array_keys($this->data);
                    $clause .= sprintf(" %s %d AND %d", $this->operator, (int)$fromValue, (int)$toValue);
                } else {
                    [$fromKey, $toKey] = array_keys($this->data);
                    $clause .= sprintf(" %s :%s AND :%s", $this->operator, $fromKey, $toKey);
                }
                break;
            case ComparisionOperator::IN:
            case ComparisionOperator::NOT_IN:
                $clause .= ' ' . $this->operator . '(';
                if ($withBindVariables) {
                    foreach ($this->data as $value) {
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
                $clause .= sprintf(" %s %s", $this->operator, key($this->data));
        }

        return $clause;
    }

    /**
     * Makes value as query part
     *
     * @param null|bool|object|string|float $value Value to make as query part
     *
     * @return string
     */
    protected function prepareRenderedValue($value)
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return (string)(int)$value;
        }

        if (is_object($value)) {
            return $this->addSlashes((string)$value);
        }

        if ($value === '') {
            return "''";
        }

        if ((strpos($value, '`') !== 0) && (substr($value, -1) !== '`')) {
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
        return str_replace(['\\', '\''], ['\\\\', '\\\''], $str);
    }

    /**
     * @inheritDoc
     */
    public function getBindData(): array
    {
        return is_string($this->data) ? [] : $this->data;
    }
}