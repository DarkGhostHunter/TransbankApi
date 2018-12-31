<?php

namespace DarkGhostHunter\TransbankApi\Helpers;

use ReflectionClass;
use Throwable;

class Helpers
{
    /**
     * Returns the Class basename without namespace
     *
     * @param string $class
     * @return string|null
     */
    public static function classBasename(string $class)
    {
        try {
            return (new ReflectionClass($class))->getShortName();
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Return the directory contents without dots directories.
     *
     * @param string $directory
     * @return array|null
     */
    public static function dirContents(string $directory)
    {
        try {
            return array_values(array_diff(scandir($directory), array('.', '..')));
        } catch (Throwable $throwable) {
            return null;
        }
    }

    /**
     * Returns if the array is an untouched numeric index
     *
     * @param array $array
     * @return bool
     */
    public static function isNumericArray(array $array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Filter an array by the given array keys
     *
     * @param array $array
     * @param string|array $keys
     * @return array
     */
    public static function arrayOnly(array $array, $keys)
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * Returns the $array without
     *
     * @param array $array
     * @param $excluded
     * @return array
     */
    public static function arrayExcept(array $array, $excluded)
    {
        return array_diff_key($array, array_flip($excluded));
    }
}