<?php

use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\ComparisionOperator;
use Imponeer\Database\Criteria\Enum\Order;
use Imponeer\Tests\Database\Criteria\Helpers\UniqueBindParam;
use PHPUnit\Framework\TestCase;

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
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], strtolower($operatorVal)];
            yield [$column, [mt_rand(PHP_INT_MIN, 0), mt_rand(1, PHP_INT_MAX)], ucfirst(strtolower($operatorVal))];
        }

        foreach ([ComparisionOperator::IN(), ComparisionOperator::NOT_IN()] as $operatorEnum) {
            foreach ($possibleValues as $value) {
                yield [$column, array_fill(0, mt_rand(1, 100), $value), $operatorEnum];
                $operatorVal = $operatorEnum->getValue();
                yield [$column, array_fill(0, mt_rand(1, 100), $value), $operatorVal];
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
        $criteria->setOrder($order);
        self::assertSame(strtoupper($order), (string)$criteria->getOrder(), 'Order ' . $order . ' does\'t sets');
    }

}