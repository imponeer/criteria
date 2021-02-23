<?php

namespace Imponeer\Database\Criteria\Enum;

use MyCLabs\Enum\Enum;

/**
 * Defines how to order results
 *
 * @package Imponeer\Database\Criteria\Enum
 *
 * @method static Order ASC()
 * @method static Order DESC()
 */
final class Order extends Enum
{

    /**
     * Sort in ASC mode
     */
    private const ASC = 'ASC';

    /**
     * Sort in DESC mode
     */
    private const DESC = 'DESC';

}