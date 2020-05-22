<?php

/**
 * This file is part of api-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/api-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

if (!function_exists('a_test_function')) {
    /**
     * A test function.
     *
     * This function does nothing in particular.
     * @param string $string A string
     * @return string The initial string with a `PHP_EOL`
     */
    function a_test_function(string $string = 'my string'): string
    {
        return $string . PHP_EOL;
    }
}

if (!function_exists('get_woof')) {
    /**
     * Gets a `Woof!`
     * @return string
     * @see https://en.wikipedia.org/wiki/Dog
     */
    function get_woof(): string
    {
        return 'Woof!';
    }
}

if (!function_exists('old_function')) {
    /**
     * An old function
     * @deprecated Use instead `a_test_function()`
     * @return void
     */
    function old_function(): void
    {
        //Does nothing
    }
}

if (!function_exists('sum_my_number')) {
    /**
     * Sums a 2 to your number
     * @param int|null $number Your number
     * @return int The result
     */
    function sum_my_number(?int $number = null): int
    {
        return ($number ?? 0) + 2;
    }
}

if (!function_exists('throw_an_exception')) {
    /**
     * This function does nothing, it only throws a exception
     * @param string|null $message Exception message
     * @return void
     * @throws Exception Exception that will always be thowned
     */
    function throw_an_exception(?string $message = null): void
    {
        throw new \Exception($message);
    }
}
