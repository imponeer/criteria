<?php

namespace Imponeer\Database\Criteria\Enum;

/**
 * Defines how to order results
 *
 * @package Imponeer\Database\Criteria\Enum
 */
enum Order: string
{
    /**
     * Sort in ASC mode
     */
    case ASC = 'ASC';

    /**
     * Sort in DESC mode
     */
    case DESC = 'DESC';
}
