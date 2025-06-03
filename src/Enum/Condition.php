<?php

namespace Imponeer\Database\Criteria\Enum;

/**
 * Rules how to join where parts
 *
 * @package Imponeer\Database\Criteria\Enum
 */
enum Condition: string
{
    /**
     * Use AND
     */
    case AND = 'AND';

    /**
     * Use OR
     */
    case OR = 'OR';

    /**
     * Use XOR
     */
    case XOR = 'XOR';
}
