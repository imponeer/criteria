<?php

namespace Imponeer\Database\Criteria\Enum;

use MyCLabs\Enum\Enum;

/**
 * SQL comparision operator
 *
 * @package Imponeer\Database\Criteria\Enum
 *
 * @method static ComparisionOperator IS_NULL()
 * @method static ComparisionOperator IS_NOT_NULL()
 * @method static ComparisionOperator IN()
 * @method static ComparisionOperator NOT_IN()
 * @method static ComparisionOperator GREATER_THAN()
 * @method static ComparisionOperator GREATER_OR_EQUAL_TO()
 * @method static ComparisionOperator LESS_THAN()
 * @method static ComparisionOperator LESS_OR_EQUAL_TO()
 * @method static ComparisionOperator EQUAL_TO()
 * @method static ComparisionOperator NOT_EQUAL_TO()
 * @method static ComparisionOperator BETWEEN()
 * @method static ComparisionOperator LIKE()
 * @method static ComparisionOperator NULL_SAFE_EQUAL_TO()
 * @method static ComparisionOperator NOT_BETWEEN()
 * @method static ComparisionOperator NOT_LIKE()
 * @method static ComparisionOperator NOT_REGEXP()
 * @method static ComparisionOperator REGEXP()
 * @method static ComparisionOperator RLIKE()
 */
class ComparisionOperator extends Enum
{

    /**
     * NULL value test
     */
    public const IS_NULL = 'IS NULL';

    /**
     * NOT NULL value test
     */
    public const IS_NOT_NULL = 'IS NOT NULL';

    /**
     * Whether a value is within a set of values
     */
    public const IN = 'IN';

    /**
     * Whether a value is not within a set of values
     */
    public const NOT_IN = 'NOT IN';

    /**
     * Greater than operator
     */
    public const GREATER_THAN = '>';

    /**
     * Greater than or equal operator
     */
    public const GREATER_OR_EQUAL_TO = '>=';

    /**
     * Less than or equal operator
     */
    public const LESS_THAN = '<';

    /**
     * Less than or equal operator
     */
    public const LESS_OR_EQUAL_TO = '<=';

    /**
     * Equal operator
     */
    public const EQUAL_TO = '=';

    /**
     * Not equal operator
     */
    public const NOT_EQUAL_TO = '!=';

    /**
     * Whether a value is within a range of values
     */
    public const BETWEEN = 'BETWEEN';

    /**
     * Simple pattern matching
     */
    public const LIKE = 'LIKE';

    /**
     * NULL-safe equal to operator
     */
    public const NULL_SAFE_EQUAL_TO = '<=>';

    /**
     *    Whether a value is not within a range of values
     */
    public const NOT_BETWEEN = 'NOT BETWEEN';

    /**
     *    Negation of simple pattern matching
     */
    public const NOT_LIKE = 'NOT LIKE';

    /**
     * Negation of REGEXP
     */
    public const NOT_REGEXP = 'NOT REGEXP';

    /**
     * REGEXP match
     */
    public const REGEXP = 'REGEXP';

    /**
     * Whether string matches regular expression
     */
    public const RLIKE = 'RLIKE';
}