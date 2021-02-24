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
     * @param CriteriaElement|null $criteria Criteria element to add at start
     * @param string $condition Join condition
     */
    public function __construct(?CriteriaElement $criteria = null, $condition = 'AND')
    {
        parent::__construct();

        if ($criteria instanceof CriteriaElement) {
            $this->add($criteria, $condition);
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
    public function add(CriteriaElement $criteriaElement, $condition = 'AND'): self
    {
        if ($condition instanceof Condition) {
            $this->elements[] = [$criteriaElement, $condition];
        } else {
            $condition = strtoupper($condition);
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