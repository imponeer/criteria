<?php

namespace Imponeer\Database\Criteria\Helpers;

/**
 * Makes bind param name unique
 *
 * @package Imponeer\Database\Criteria\Helpers
 */
final class UniqueBindParam
{
    /**
     * Last generated bind suffix ID
     */
    private static int $lastGeneratedBindSuffixId = 0;

    /**
     * UniqueBindParam constructor.
     */
    private function __construct()
    {
    }

    /**
     * Generates unique bind name from column name
     *
     * @param string $column Column name
     */
    public static function generate(string $column): string
    {
        return 'col_' . $column . '_' . (++self::$lastGeneratedBindSuffixId);
    }
}
