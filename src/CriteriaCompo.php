<?php

namespace Imponeer\Database\Criteria;

use Exception;
use Imponeer\Database\Criteria\Enum\Condition;
use Traversable;

/**
 * Criteria elements collection
 *
 * @package Imponeer\Database\Criteria
 */
class CriteriaCompo extends CriteriaElement implements \IteratorAggregate
{
    /**
     * @var array<CriteriaElement,string>[]
     */
    protected array $elements = [];

    /**
     * Constructor
     *
     * @param CriteriaElement|null $criteria Criteria element to add at the start
     * @param Condition|string $condition Join condition
     */
    public function __construct(?CriteriaElement $criteria = null, Condition|string $condition = Condition::AND)
    {
        parent::__construct();

        if ($criteria instanceof CriteriaElement) {
            $this->add($criteria, $condition);
        }
    }

    /**
     * Add a criteria element to a collection
     *
     * @param CriteriaElement $criteriaElement Criteria element to add
     * @param string|Condition $condition Condition
     *
     * @return $this
     */
    public function add(CriteriaElement $criteriaElement, Condition|string $condition = Condition::AND): self
    {
        $this->elements[] = [
            $criteriaElement,
            $condition instanceof Condition ? $condition : Condition::from(strtoupper(trim($condition)))
        ];

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
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
     *
     * @return Traversable<string, CriteriaElement>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->elements as $item) {
            yield $item[1] => $item[0];
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function render(bool $withBindVariables = false): ?string
    {
        $ret = '';
        $first = true;
        foreach ($this->getIterator() as $join => $element) {
            if ($first) {
                $first = false;
            } else {
                $ret .= ' ' . ($join instanceof Condition ? $join->value : $join) . ' ';
            }
            $ret .= '(' . $element->render($withBindVariables) . ')';
        }

        return $ret;
    }
}