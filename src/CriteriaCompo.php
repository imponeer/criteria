<?php

namespace Imponeer\Database\Criteria;

use Imponeer\Database\Criteria\Enum\Condition;

/**
 * Criteria elements collection
 *
 * @package Imponeer\Database\Criteria
 */
class CriteriaCompo extends CriteriaElement implements \IteratorAggregate
{
    /**
     * @var [CriteriaElement,string][]
     */
    protected $elements = [];

    /**
     * Constructor
     *
     * @param object $ele
     * @param string $condition
     */
    public function __construct($ele = null, $condition = 'AND')
    {
        parent::__construct();

        if ($ele instanceof CriteriaElement) {
            $this->add($ele, $condition);
        }
    }

    /**
     * Add criteria element to collection
     *
     * @param CriteriaElement $criteriaElement Criteria element to add
     * @param string|Condition $condition Condition
     *
     * @return $this
     */
    public function add(CriteriaElement $criteriaElement, $condition = 'AND')
    {
        if ($condition instanceof Condition) {
            $this->elements[] = [$criteriaElement, $condition];
        } else {
            Condition::assertValidValue($condition);
            $this->elements[] = [$criteriaElement, Condition::from($condition)];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBindData(): array
    {
        $ret = [];
        foreach ($this->getIterator() as $element) {
            foreach ($element->getBindData() as $k => $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        foreach ($this->elements as $item) {
            yield $item[1] => $item[0];
        }
    }

    /**
     * @inheritDoc
     */
    public function render(bool $withBindVariables = false): ?string
    {
        $ret = '';
        $first = true;
        foreach ($this->getIterator() as $join => $element) {
            if ($first) {
                $first = false;
            } else {
                $ret .= ' ' . $join . ' ';
            }
            $ret .= '(' . $element->render($withBindVariables) . ')';
        }

        return $ret;
    }
}