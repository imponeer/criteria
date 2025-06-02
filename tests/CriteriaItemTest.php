<?php

namespace Imponeer\Tests\Database\Criteria;

use Generator;
use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\ComparisonOperator;
use Imponeer\Database\Criteria\Enum\Order;
use JsonException;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use stdClass;

class CriteriaItemTest extends TestCase
{

    /**
     * Gets all possible comparison operators enums
     *
     * @return Generator
     * @throws RandomException
     */
    final public function provideComparisionOperators(): Generator
    {
        $column = sha1(random_int(PHP_INT_MIN, PHP_INT_MAX));
        $specialOperators = [
            ComparisonOperator::BETWEEN->name,
            ComparisonOperator::NOT_BETWEEN->name,
            ComparisonOperator::IN->name,
            ComparisonOperator::NOT_IN->name,
        ];
        $possibleValues = [
            null,  // null value
            md5(random_int(PHP_INT_MIN, PHP_INT_MAX)),  // random string
            random_int(PHP_INT_MIN, PHP_INT_MAX), // random int
            mt_rand() / mt_getrandmax(), // random float
            true, // true
            [], // array
            new stdClass(), // class
        ];

        foreach (ComparisonOperator::cases() as $operatorEnum) {
            if (in_array($operatorEnum->name, $specialOperators, true)) {
                continue;
            }
            $operatorVal = $operatorEnum->value;
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

        foreach ([ComparisonOperator::BETWEEN, ComparisonOperator::NOT_BETWEEN] as $operatorEnum) {
            yield [$column, [random_int(PHP_INT_MIN, 0), random_int(1, PHP_INT_MAX)], $operatorEnum];
            $operatorVal = $operatorEnum->value;
            yield [$column, [random_int(PHP_INT_MIN, 0), random_int(1, PHP_INT_MAX)], $operatorVal];
            yield [$column, [random_int(PHP_INT_MIN, 0), random_int(1, PHP_INT_MAX)], ' ' . $operatorVal . ' '];
            yield [$column, [random_int(PHP_INT_MIN, 0), random_int(1, PHP_INT_MAX)], strtolower($operatorVal)];
            yield [$column, [random_int(PHP_INT_MIN, 0), random_int(1, PHP_INT_MAX)], ucfirst(strtolower($operatorVal))];
        }

        foreach ([ComparisonOperator::IN, ComparisonOperator::NOT_IN] as $operatorEnum) {
            foreach ($possibleValues as $value) {
                yield [$column, array_fill(0, random_int(1, 100), $value), $operatorEnum];
                $operatorVal = $operatorEnum->value;
                yield [$column, array_fill(0, random_int(1, 100), $value), $operatorVal];
                yield [$column, array_fill(0, random_int(1, 100), $value), ' ' . $operatorVal . ' '];
                yield [$column, array_fill(0, random_int(1, 100), $value), strtolower($operatorVal)];
                yield [$column, array_fill(0, random_int(1, 100), $value), ucfirst(strtolower($operatorVal))];
            }
        }
    }

    /**
     * Test if enum can render all comparison operators as object
     *
     * @param string $column Column name
     * @param mixed $value Value to use
     * @param string|ComparisonOperator $operator Comparison operator to be used for test
     *
     * @dataProvider provideComparisionOperators
     *
     * @throws JsonException
     */
    final public function testIfOperatorRendersContent(string $column, mixed $value, ComparisonOperator|string $operator): void
    {
        $criteria = new CriteriaItem($column, $value, $operator);
        self::assertNotEmpty(
            $criteria->render(false),
            'Criteria with condition ' . $criteria->getComparisonOperator()->name . ' doesn\'t renders SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(false),
            'Criteria with condition ' . $criteria->getComparisonOperator()->name . ' doesn\'t renders WHERE SQL (without binds)'
        );
        self::assertNotEmpty(
            $criteria->render(true),
            'Criteria with condition ' . $criteria->getComparisonOperator()->name . ' doesn\'t renders SQL (with binds)'
        );
        self::assertNotEmpty(
            $criteria->renderWhere(true),
            'Criteria with condition ' . $criteria->getComparisonOperator()->name . ' doesn\'t renders WHERE SQL (with binds)'
        );
    }

    /**
     * Provides order test data
     *
     * @return Generator
     */
    final public function provideOrder(): Generator
    {
        foreach (Order::cases() as $order) {
            yield [$order->value];
            yield [strtolower($order->value)];
            yield [ucfirst(strtolower($order->value))];
            yield [' ' . $order->value . ' '];
        }
    }

    /**
     * Tests order with enums
     *
     * @param string|Order $order
     *
     * @dataProvider provideOrder
     *
     * @throws RandomException
     */
    final public function testOrder(Order|string $order): void
    {
        $criteria = new CriteriaItem(sha1(random_int(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertSame(Order::ASC->value, $criteria->getOrder()->value, 'Default order is not correct');
        $criteria->setOrder($order);
        self::assertSame(strtoupper(trim($order)), $criteria->getOrder()->value, 'Order ' . $order . ' does\'t sets');
    }

    /**
     * Tests group by operations
     *
     * @throws RandomException
     */
    final public function testGroupBy(): void
    {
        $criteria = new CriteriaItem(sha1(random_int(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertEmpty($criteria->getGroupBy(), 'Default group by is not empty');
        $groupBy = sha1(random_int(PHP_INT_MIN, PHP_INT_MAX));
        $criteria->setGroupBy($groupBy);
        self::assertNotEmpty($criteria->getGroupBy(), 'Group by was set but value wasn\'t modified');
        self::assertStringStartsWith('GROUP BY', trim($criteria->getGroupBy()), 'Non empty group by doesn\' starts with "GROUP BY"');
        self::assertStringContainsString($groupBy, $criteria->getGroupBy(), 'Group by value doesn\'t exists');
    }

    /**
     * Tests sort by operations
     * @throws RandomException
     */
    final public function testSortBy(): void
    {
        $criteria = new CriteriaItem(sha1(random_int(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertEmpty($criteria->getSort(), 'Default sort by is not empty');
        $sort = sha1(random_int(PHP_INT_MIN, PHP_INT_MAX));
        $criteria->setSort($sort);
        self::assertNotEmpty($criteria->getSort(), 'Sort by was set but value wasn\'t modified');
        self::assertStringContainsString($sort, $criteria->getSort(), 'Sort by value doesn\'t exists');
    }

    /**
     * Tests limit/from by operations
     * @throws RandomException
     */
    final public function testPartialResults(): void
    {
        $criteria = new CriteriaItem(sha1(random_int(PHP_INT_MIN, PHP_INT_MAX)));
        self::assertSame(0, $criteria->getLimit(), 'Default limit is not 0');
        self::assertSame(0, $criteria->getStart(), 'Default start is not 0');
        $limit = random_int(1, PHP_INT_MAX);
        $start = random_int(1, PHP_INT_MAX);
        $criteria->setLimit($limit)->setStart($start);
        self::assertSame($limit, $criteria->getLimit(), 'Updated limit is not same as should be');
        self::assertSame($start, $criteria->getStart(), 'Updated start is not same as should be');
        $criteria->setLimit()->setStart();
        self::assertSame(0, $criteria->getLimit(), 'Reset limit is not 0');
        self::assertSame(0, $criteria->getStart(), 'Reset start is not 0');
    }

}