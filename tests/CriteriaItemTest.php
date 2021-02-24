<?php

use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\ComparisionOperator;
use Imponeer\Tests\Database\Criteria\Helpers\UniqueBindParam;
use PHPUnit\Framework\TestCase;

class CriteriaItemTest extends TestCase
{

    /**
     * Gets all possible comparision operators
     *
     * @return Generator
     */
    public function getComparisionOperators()
    {
        $column = sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX));
        $specialOperators = [
            ComparisionOperator::BETWEEN()->getKey(),
            ComparisionOperator::NOT_BETWEEN()->getKey(),
            ComparisionOperator::IN()->getKey(),
            ComparisionOperator::NOT_IN()->getKey(),
        ];
        $possibleValues = [
            null,  // null value
            md5(mt_rand(PHP_INT_MIN, PHP_INT_MAX)),  // random string
            mt_rand(PHP_INT_MIN, PHP_INT_MAX), // random int
            mt_rand() / mt_getrandmax(), // random float
            true, // true
            [], // array
            new stdClass(), // class
        ];

        foreach (ComparisionOperator::values() as $operator) {
            if (in_array($operator->getKey(), $specialOperators, true)) {
                continue;
            }
            foreach ($possibleValues as $value) {
                yield [$column, $value, $operator];
            }
        }

        foreach ([ComparisionOperator::BETWEEN(), ComparisionOperator::NOT_BETWEEN()] as $operator) {
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], $operator];
        }

        foreach ([ComparisionOperator::IN(), ComparisionOperator::NOT_IN()] as $operator) {
            foreach ($possibleValues as $value) {
                yield [$column, array_fill(0, mt_rand(1, 100), $value), $operator];
            }
        }
    }

    /**
     * Test if all comparision operators can be rendered by enum as object
     *
     * @param string $column Column name
     * @param mixed $value Value to use
     * @param ComparisionOperator $operator Comparision operator to be used for test
     *
     * @dataProvider getComparisionOperators
     */
    public function testIfOperatorRendersContentAsEnum(string $column, $value, ComparisionOperator $operator)
    {
        $criteria = new CriteriaItem($column, $value, $operator);
        self::assertNotEmpty(
            $criteria->render(false),
            'Criteria with condition ' . $operator->getKey() . ' doesn\'t renders SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(false),
            'Criteria with condition ' . $operator->getKey() . ' doesn\'t renders WHERE SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->render(true),
            'Criteria with condition ' . $operator->getKey() . ' doesn\'t renders SQL (with binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(true),
            'Criteria with condition ' . $operator->getKey() . ' doesn\'t renders WHERE SQL (with binds)'
        );
    }

    /**
     * Test if all comparision operators can be rendered by enum as string
     *
     * @param string $column Column name
     * @param mixed $value Value to use
     * @param ComparisionOperator $operator Comparision operator to be used for test
     *
     * @dataProvider getComparisionOperators
     */
    public function testIfOperatorRendersContentAsString(string $column, $value, ComparisionOperator $operator)
    {
        $criteria = new CriteriaItem($column, $value, $operator->getValue());

        self::assertNotEmpty(
            $criteria->render(false),
            'Criteria with condition ' . $operator->getValue() . ' doesn\'t renders SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(false),
            'Criteria with condition ' . $operator->getValue() . ' doesn\'t renders WHERE SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->render(true),
            'Criteria with condition ' . $operator->getValue() . ' doesn\'t renders SQL (with binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(true),
            'Criteria with condition ' . $operator->getValue() . ' doesn\'t renders WHERE SQL (with binds)'
        );
    }

}