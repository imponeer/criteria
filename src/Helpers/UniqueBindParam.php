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
     *
     * @var int
     */
    static private $lastGeneratedBindSuffixId = 0;

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
     *
     * @return string
     */
    public static function generate(string $column): string
    {
        return $column . '_' . (++self::$lastGeneratedBindSuffixId);
    }

}