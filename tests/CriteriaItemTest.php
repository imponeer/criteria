<?php

namespace Imponeer\Tests\Database\Criteria;

use Faker\Factory;
use Generator;
use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\ComparisonOperator;
use Imponeer\Database\Criteria\Enum\Order;
use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use stdClass;

class CriteriaItemTest extends TestCase
{
    /**
     * Gets all possible comparison operators enums
     *
     * @return array<string, array{0: string, 1: mixed, 2: ComparisonOperator|string}>
     * @throws RandomException
     * @throws JsonException
     */
    final public static function provideComparisonOperators(): array
    {
        $faker = Factory::create();

        $column = $faker->sha1();
        $specialOperators = [
            ComparisonOperator::BETWEEN->name,
            ComparisonOperator::NOT_BETWEEN->name,
            ComparisonOperator::IN->name,
            ComparisonOperator::NOT_IN->name,
        ];
        $possibleValues = [
            null,  // null value
            $faker->md5(),  // random string
            $faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX), // random int
            $faker->randomFloat(), // random float
            true, // true
            [], // array
            new stdClass(), // class
        ];

        $testsVariations = [];
        $addTestVariation = static function (
            string $column,
            mixed $value,
            ComparisonOperator|string $operator
        ) use (&$testsVariations) {
            $data = [
                'column' => $column,
                'value' => $value,
                'operator' => $operator,
            ];
            $label = json_encode($data, JSON_THROW_ON_ERROR);
            $testsVariations[$label] = array_values($data);
        };

        foreach (ComparisonOperator::cases() as $operatorEnum) {
            if (in_array($operatorEnum->name, $specialOperators, true)) {
                continue;
            }
            $operatorVal = $operatorEnum->value;
            foreach ($possibleValues as $value) {
                $addTestVariation($column, $value, $operatorEnum);
                $addTestVariation($column, $value, $operatorVal);
                $addTestVariation($column, $value, ' ' . $operatorVal . ' ');
                if (strtolower($operatorVal) !== $operatorVal) {
                    $addTestVariation($column, $value, strtolower($operatorVal));
                    $addTestVariation($column, $value, ucfirst(strtolower($operatorVal)));
                }
            }
        }

        foreach ([ComparisonOperator::BETWEEN, ComparisonOperator::NOT_BETWEEN] as $operatorEnum) {
            $range = [
                $faker->numberBetween(PHP_INT_MIN, 0),
                $faker->numberBetween(1, PHP_INT_MAX),
            ];

            $addTestVariation($column, $range, $operatorEnum);
            $operatorVal = $operatorEnum->value;
            $addTestVariation($column, $range, $operatorVal);
            $addTestVariation($column, $range, ' ' . $operatorVal . ' ');
            $addTestVariation($column, $range, strtolower($operatorVal));
            $addTestVariation($column, $range, ucfirst(strtolower($operatorVal)));
        }

        foreach ([ComparisonOperator::IN, ComparisonOperator::NOT_IN] as $operatorEnum) {
            foreach ($possibleValues as $value) {
                $inData = array_fill(
                    0,
                    $faker->numberBetween(1, 5),
                    $value
                );

                $addTestVariation($column, $inData, $operatorEnum);
                $operatorVal = $operatorEnum->value;
                $addTestVariation($column, $inData, $operatorVal);
                $addTestVariation($column, $inData, ' ' . $operatorVal . ' ');
                $addTestVariation($column, $inData, strtolower($operatorVal));
                $addTestVariation($column, $inData, ucfirst(strtolower($operatorVal)));
            }
        }

        return $testsVariations;
    }

    /**
     * Test if enum can render all comparison operators as an object
     *
     * @param string $column Column name
     * @param mixed $value Value to use
     * @param string|ComparisonOperator $operator Comparison operator to be used for test
     *
     * @throws JsonException
     */
    #[DataProvider('provideComparisonOperators')]
    final public function testIfOperatorRendersContent(
        string $column,
        mixed $value,
        ComparisonOperator|string $operator
    ): void {
        $criteria = new CriteriaItem($column, $value, $operator);
        self::assertNotEmpty(
            $criteria->render(false),
            sprintf(
                "Criteria with condition %s doesn't renders SQL (without binds)",
                $criteria->getComparisonOperator()->name
            )
        );
        self::assertNotEmpty(
            $criteria->renderWhere(false),
            sprintf(
                "Criteria with condition %s doesn't renders WHERE SQL (without binds)",
                $criteria->getComparisonOperator()->name
            )
        );
        self::assertNotEmpty(
            $criteria->render(true),
            sprintf(
                "Criteria with condition %s doesn't renders SQL (with binds)",
                $criteria->getComparisonOperator()->name
            )
        );
        self::assertNotEmpty(
            $criteria->renderWhere(true),
            sprintf(
                "Criteria with condition %s doesn't renders WHERE SQL (with binds)",
                $criteria->getComparisonOperator()->name
            )
        );
    }

    /**
     * Provides order test data
     *
     * @return Generator
     */
    final public static function provideOrder(): Generator
    {
        foreach (Order::cases() as $order) {
            yield $order->value => [$order->value];
            yield strtolower($order->value) => [strtolower($order->value)];
            yield ucfirst(strtolower($order->value)) => [ucfirst(strtolower($order->value))];
            yield ' ' . $order->value . ' ' => [' ' . $order->value . ' '];
        }
    }

    /**
     * Tests order with enums
     *
     * @param string|Order $order
     */
    #[DataProvider('provideOrder')]
    final public function testOrder(Order|string $order): void
    {
        $faker = Factory::create();

        $criteria = new CriteriaItem($faker->sha1());
        self::assertSame(Order::ASC->value, $criteria->getOrder()->value, 'Default order is not correct');
        $criteria->setOrder($order);
        self::assertSame(strtoupper(trim($order)), $criteria->getOrder()->value, 'Order ' . $order . ' does\'t sets');
    }

    /**
     * Tests group by operations
     */
    final public function testGroupBy(): void
    {
        $faker = Factory::create();
        $criteria = new CriteriaItem($faker->sha1());
        self::assertEmpty($criteria->getGroupBy(), 'Default group by is not empty');
        $groupBy = $faker->sha1();
        $criteria->setGroupBy($groupBy);
        self::assertNotEmpty($criteria->getGroupBy(), 'Group by was set but value wasn\'t modified');
        self::assertStringStartsWith(
            'GROUP BY',
            trim($criteria->getGroupBy()),
            'Non empty group by doesn\' starts with "GROUP BY"'
        );
        self::assertStringContainsString($groupBy, $criteria->getGroupBy(), 'Group by value doesn\'t exists');
    }

    /**
     * Tests sort by operations
     */
    final public function testSortBy(): void
    {
        $faker = Factory::create();
        $criteria = new CriteriaItem($faker->sha1());
        ;
        self::assertEmpty($criteria->getSort(), 'Default sort by is not empty');
        $sort = $faker->sha1();
        $criteria->setSort($sort);
        self::assertNotEmpty($criteria->getSort(), 'Sort by was set but value wasn\'t modified');
        self::assertStringContainsString($sort, $criteria->getSort(), 'Sort by value doesn\'t exists');
    }

    /**
     * Tests limit/from by operations
     */
    final public function testPartialResults(): void
    {
        $faker = Factory::create();
        $criteria = new CriteriaItem($faker->sha1());
        self::assertSame(0, $criteria->getLimit(), 'Default limit is not 0');
        self::assertSame(0, $criteria->getStart(), 'Default start is not 0');
        $limit = $faker->numberBetween(1, PHP_INT_MAX);
        $start = $faker->numberBetween(1, PHP_INT_MAX);
        $criteria->setLimit($limit)->setStart($start);
        self::assertSame($limit, $criteria->getLimit(), 'Updated limit is not same as should be');
        self::assertSame($start, $criteria->getStart(), 'Updated start is not same as should be');
        $criteria->setLimit()->setStart();
        self::assertSame(0, $criteria->getLimit(), 'Reset limit is not 0');
        self::assertSame(0, $criteria->getStart(), 'Reset start is not 0');
    }
}
