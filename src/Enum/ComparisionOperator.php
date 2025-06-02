<?php

namespace Imponeer\Database\Criteria\Enum;

/**
 * SQL comparison operator
 *
 * @package Imponeer\Database\Criteria\Enum
 */
enum ComparisionOperator: string
{

    /**
     * NULL value test
     */
    case IS_NULL = 'IS NULL';

    /**
     * NOT NULL value test
     */
    case IS_NOT_NULL = 'IS NOT NULL';

    /**
     * Whether a value is within a set of values
     */
    case IN = 'IN';

    /**
     * Whether a value is not within a set of values
     */
    case NOT_IN = 'NOT IN';

    /**
     * Greater than operator
     */
    case GREATER_THAN = '>';

    /**
     * Greater than or equal operator
     */
    case GREATER_OR_EQUAL_TO = '>=';

    /**
     * Less than or equal operator
     */
    case LESS_THAN = '<';

    /**
     * Less than or equal operator
     */
    case LESS_OR_EQUAL_TO = '<=';

    /**
     * Equal operator
     */
    case EQUAL_TO = '=';

    /**
     * Not equal operator
     */
    case NOT_EQUAL_TO = '!=';

    /**
     * Whether a value is within a range of values
     */
    case BETWEEN = 'BETWEEN';

    /**
     * Simple pattern matching
     */
    case LIKE = 'LIKE';

    /**
     * NULL-safe equal to operator
     */
    case NULL_SAFE_EQUAL_TO = '<=>';

    /**
     *    Whether a value is not within a range of values
     */
    case NOT_BETWEEN = 'NOT BETWEEN';

    /**
     *    Negation of simple pattern matching
     */
    case NOT_LIKE = 'NOT LIKE';

    /**
     * Negation of REGEXP
     */
    case NOT_REGEXP = 'NOT REGEXP';

    /**
     * REGEXP match
     */
    case REGEXP = 'REGEXP';

    /**
     * Whether string matches regular expression
     */
    case RLIKE = 'RLIKE';
}