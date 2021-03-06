<?php

namespace Imponeer\Tests\Database\Criteria;

use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\ComparisionOperator;
use Imponeer\Database\Criteria\Enum\Order;
use PHPUnit\Framework\TestCase;
use stdClass;

class CriteriaItemTest extends TestCase
{

    /**
     * Gets all possible comparision operators enums
     *
     * @return Generator
     */
    public function provideComparisionOperators()
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

        foreach (ComparisionOperator::values() as $operatorEnum) {
            if (in_array($operatorEnum->getKey(), $specialOperators, true)) {
                continue;
            }
            $operatorVal = $operatorEnum->getValue();
            foreach ($possibleValues as $value) {
                yield [$column, $value, $operatorEnum];
                yield [$column, $value, $operatorVal];
                yield [$column, $value, ' ' . $operatorVal . ' '];
                if (strtolower($operatorVal) !== $operatorVal) {
                    yield [$column, $value, strtolower($operatorVal)];
                    yield [$column, $value, ucfirst(strtolower($operatorVal))];
                }
            }
        }

        foreach ([ComparisionOperator::BETWEEN(), ComparisionOperator::NOT_BETWEEN()] as $operatorEnum) {
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], $operatorEnum];
            $operatorVal = $operatorEnum->getValue();
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], $operatorVal];
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], ' ' . $operatorVal . ' '];
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], strtolower($operatorVal)];
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], ucfirst(strtolower($operatorVal))];
        }

        foreach ([ComparisionOperator::IN(), ComparisionOperator::NOT_IN()] as $operatorEnum) {
            foreach ($possibleValues as $value) {
                yield [$column, array_fill(0, mt_rand(1, 100), $value), $operatorEnum];
                $operatorVal = $operatorEnum->getValue();
                yield [$column, array_fill(0, mt_rand(1, 100), $value), $operatorVal];
                yield [$column, array_fill(0, mt_rand(1, 100), $value), ' ' . $operatorVal . ' '];
                yield [$column, array_fill(0, mt_rand(1, 100), $value), strtolower($operatorVal)];
                yield [$column, array_fill(0, mt_rand(1, 100), $value), ucfirst(strtolower($operatorVal))];
            }
        }
    }

    /**
     * Test if all comparision operators can be rendered by enum as object
     *
     * @param string $column Column name
     * @param mixed $value Value to use
     * @param ComparisionOperator|string $operator Comparision operator to be used for test
     *
     * @dataProvider provideComparisionOperators
     */
    public function testIfOperatorRendersContent(string $column, $value, $operator)
    {
        $criteria = new CriteriaItem($column, $value, $operator);
        self::assertNotEmpty(
            $criteria->render(false),
            'Criteria with condition ' . $operator . ' doesn\'t renders SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(false),
            'Criteria with condition ' . $operator . ' doesn\'t renders WHERE SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->render(true),
            'Criteria with condition ' . $operator . ' doesn\'t renders SQL (with binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(true),
            'Criteria with condition ' . $operator . ' doesn\'t renders WHERE SQL (with binds)'
        );
    }

    /**
     * Provides order test data
     *
     * @return Generator
     */
    public function provideOrder()
    {
        foreach (Order::values() as $order) {
            yield [$order];
            yield [strtolower($order)];
            yield [ucfirst(strtolower($order))];
            yield [' ' . $order . ' '];
        }
    }

    /**
     * Tests order with enums
     *
     * @param Order|string $order
     *
     * @dataProvider provideOrder
     */
    public function testOrder($order)
    {
        $criteria = new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertSame(Order::ASC()->getValue(), (string)$criteria->getOrder(), 'Default order is not correct');
        $criteria->setOrder($order);
        self::assertSame(strtoupper(trim($order)), (string)$criteria->getOrder(), 'Order ' . $order . ' does\'t sets');
    }

    /**
     * Tests group by operations
     */
    public function testGroupBy()
    {
        $criteria = new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertEmpty($criteria->getGroupBy(), 'Default group by is not empty');
        $groupBy = sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX));
        $criteria->setGroupBy($groupBy);
        self::assertNotEmpty($criteria->getGroupBy(), 'Group by was set but value wasn\'t modified');
        self::assertStringStartsWith('GROUP BY', trim($criteria->getGroupBy()), 'Non empty group by doesn\' starts with "GROUP BY"');
        self::assertStringContainsString($groupBy, $criteria->getGroupBy(), 'Group by value doesn\'t exists');
    }

    /**
     * Tests sort by operations
     */
    public function testSortBy()
    {
        $criteria = new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertEmpty($criteria->getSort(), 'Default sort by is not empty');
        $sort = sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX));
        $criteria->setSort($sort);
        self::assertNotEmpty($criteria->getSort(), 'Sort by was set but value wasn\'t modified');
        self::assertStringContainsString($sort, $criteria->getSort(), 'Sort by value doesn\'t exists');
    }

    /**
     * Tests limit/from by operations
     */
    public function testPartialResults()
    {
        $criteria = new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertSame(0, $criteria->getLimit(), 'Default limit is not 0');
        self::assertSame(0, $criteria->getStart(), 'Default start is not 0');
        $limit = mt_rand(1, PHP_INT_MAX);
        $start = mt_rand(1, PHP_INT_MAX);
        $criteria->setLimit($limit)->setStart($start);
        self::assertSame($limit, $criteria->getLimit(), 'Updated limit is not same as should be');
        self::assertSame($start, $criteria->getStart(), 'Updated start is not same as should be');
        $criteria->setLimit()->setStart();
        self::assertSame(0, $criteria->getLimit(), 'Reset limit is not 0');
        self::assertSame(0, $criteria->getStart(), 'Reset start is not 0');
    }

}