<?php

namespace Imponeer\Database\Criteria\Enum;

use MyCLabs\Enum\Enum;

/**
 * Rules how to join where parts
 *
 * @package Imponeer\Database\Criteria\Enum
 *
 * @method static Condition AND ();
 * @method static Condition OR ();
 * @method static Condition XOR ();
 */
class Condition extends Enum
{

    /**
     * Use AND
     */
    private const AND = 'AND';

    /**
     * Use OR
     */
    private const OR = 'OR';

    /**
     * Use XOR
     */
    private const XOR = 'XOR';

}