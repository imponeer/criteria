<?php

namespace Imponeer\Tests\Database\Criteria;

use Exception;
use Faker\Factory;
use Generator;
use Imponeer\Database\Criteria\CriteriaCompo;
use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\Condition;
use Imponeer\Database\Criteria\Enum\Order;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CriteriaCompoTest extends TestCase
{
    /**
     * Provides Condition test data
     *
     * @return array<string, array{0: CriteriaCompo, 1: string|Condition}>
     */
    final public static function provideCondition(): array
    {
        $testsVariations = [];
        $faker = Factory::create();

        foreach (Condition::cases() as $conditionData) {
            $condition = $conditionData->value;
            $conditionVariants = [
                $condition,
                ' ' . $condition . ' ',
                strtolower($condition),
                ucfirst(strtolower($condition)),
            ];
            foreach ($conditionVariants as $conditionVar) {
                // first variant for compo
                $compo1 = new CriteriaCompo();
                $compo1->add(new CriteriaItem($faker->sha1()));
                $compo1->add(new CriteriaItem($faker->sha1()), $conditionVar);
                $label = sprintf('Two columns with null values and condition %s', $conditionData->name);
                $testsVariations[$label] = [$compo1, $conditionVar];

                // 2nd variant for compo
                $compo2 = new CriteriaCompo(new CriteriaItem($faker->sha1()));
                $compo2->add(new CriteriaItem($faker->sha1()), $conditionVar);
                $label = sprintf("One column with null value and condition %s", $conditionData->name);
                $testsVariations[$label] = [$compo2, $conditionVar];

                // 3nd variant for compo
                $compo3 = new CriteriaCompo();
                $compo3->add($compo1);
                $compo3->add($compo2, $conditionVar);
                $label = sprintf('Joined two criteria with condition %s', $conditionData->name);
                $testsVariations[$label] = [$compo3, $conditionVar];

                // 4nd variant for compo
                $compo4 = new CriteriaCompo($compo1);
                $compo4->add($compo2, $conditionVar);
                $label = sprintf('Appended two criteria with condition %s', $conditionData->name);
                $testsVariations[$label] = [$compo4, $conditionVar];
            }
        }

        return $testsVariations;
    }

    /**
     * Tests condition
     *
     * @param CriteriaCompo $compo Build criteria with such condition
     * @param string|Condition $condition join condition
     *
     * @throws Exception
     */
    #[DataProvider('provideCondition')]
    final public function testCondition(CriteriaCompo $compo, string|Condition $condition): void
    {
        if (is_string($condition)) {
            $condition = Condition::from(strtoupper(trim($condition)));
        }

        self::assertNotEmpty(
            $compo->render(false),
            'Criteria with condition ' . $condition->name . ' doesn\'t renders SQL (without binds)'
        );
        self::assertNotEmpty(
            $compo->renderWhere(false),
            'Criteria with condition ' . $condition->name . ' doesn\'t renders WHERE SQL (without binds)'
        );
        self::assertNotEmpty(
            $compo->render(true),
            'Criteria with condition ' . $condition->name . ' doesn\'t renders SQL (with binds)'
        );
        self::assertNotEmpty(
            $compo->renderWhere(true),
            'Criteria with condition ' . $condition->name . ' doesn\'t renders WHERE SQL (with binds)'
        );
    }

    /**
     * Tests criteria compo render with empty elements
     *
     * @throws Exception
     */
    final public function testEmptyRender(): void
    {
        $criteria = new CriteriaCompo();
        self::assertEmpty($criteria->render(false), 'Should render empty string (without binding)');
        self::assertEmpty($criteria->renderWhere(false), 'Should render WHERE empty string (without binding)');
        self::assertEmpty($criteria->render(true), 'Should render empty string (with binding)');
        self::assertEmpty($criteria->renderWhere(true), 'Should render WHERE empty string (with binding)');
        self::assertEmpty($criteria->getBindData(), 'Should return empty binding data');
    }

    /**
     * Provides order test data
     *
     * @return Generator
     */
    final public static function provideOrder(): Generator
    {
        foreach (Order::cases() as $order) {
            yield $order->name => [$order->value];
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
        $criteria = new CriteriaCompo();
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

        $criteria = new CriteriaCompo();
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

        $criteria = new CriteriaCompo();
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

        $criteria = new CriteriaCompo();
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
