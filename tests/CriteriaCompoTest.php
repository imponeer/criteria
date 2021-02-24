<?php

namespace Imponeer\Tests\Database\Criteria;

use Generator;
use Imponeer\Database\Criteria\CriteriaCompo;
use Imponeer\Database\Criteria\CriteriaItem;
use Imponeer\Database\Criteria\Enum\Condition;
use Imponeer\Database\Criteria\Enum\Order;
use PHPUnit\Framework\TestCase;

class CriteriaCompoTest extends TestCase
{
    /**
     * Provides Condition test data
     *
     * @return Generator
     */
    public function provideCondition()
    {
        foreach (Condition::values() as $condition) {
            foreach ([$condition, strtolower($condition), ucfirst(strtolower($condition))] as $conditionVar) {
                // first variant for compo
                $compo1 = new CriteriaCompo();
                $compo1->add(new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX))));
                $compo1->add(new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX))), $conditionVar);
                yield [$compo1, $conditionVar];

                // 2nd variant for compo
                $compo2 = new CriteriaCompo(new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX))));
                $compo2->add(new CriteriaItem(sha1(mt_rand(PHP_INT_MIN, PHP_INT_MAX))), $conditionVar);
                yield [$compo2, $conditionVar];

                // 3nd variant for compo
                $compo3 = new CriteriaCompo();
                $compo3->add($compo1);
                $compo3->add($compo2, $conditionVar);
                yield [$compo3, $conditionVar];

                // 4nd variant for compo
                $compo4 = new CriteriaCompo($compo1);
                $compo4->add($compo2, $conditionVar);
                yield [$compo4, $conditionVar];
            }
        }
    }

    /**
     * Tests condition
     *
     * @param CriteriaCompo $compo Build criteria with such condition
     * @param string|Condition $condition join condition
     *
     * @dataProvider provideCondition
     */
    public function testCondition(CriteriaCompo $compo, $condition)
    {
        self::assertNotEmpty(
            $compo->render(false),
            'Criteria with condition ' . $condition . ' doesn\'t renders SQL (without binds)'
        );
        self::assertNotEmpty(
            $compo->renderWhere(false),
            'Criteria with condition ' . $condition . ' doesn\'t renders WHERE SQL (without binds)'
        );
        self::assertNotEmpty(
            $compo->render(true),
            'Criteria with condition ' . $condition . ' doesn\'t renders SQL (with binds)'
        );
        self::assertNotEmpty(
            $compo->renderWhere(true),
            'Criteria with condition ' . $condition . ' doesn\'t renders WHERE SQL (with binds)'
        );
        //var_dump($compo->renderWhere(true));
    }

    /**
     * Tests criteria compo render with empty elements
     */
    public function testEmptyRender()
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
        $criteria = new CriteriaCompo();
        self::assertSame(Order::ASC()->getValue(), (string)$criteria->getOrder(), 'Default order is not correct');
        $criteria->setOrder($order);
        self::assertSame(strtoupper($order), (string)$criteria->getOrder(), 'Order ' . $order . ' does\'t sets');
    }

    /**
     * Tests group by operations
     */
    public function testGroupBy()
    {
        $criteria = new CriteriaCompo();
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
        $criteria = new CriteriaCompo();
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
        $criteria = new CriteriaCompo();
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