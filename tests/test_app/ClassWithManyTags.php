<?php

/**
 * This file is part of php-doc-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-doc-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

/**
 * A class with many tags
 * @method void noExistingMethod() A no existin method
 * @link https://www.php.net/manual/en/language.oop5.php
 * @since 1.2.0
 * @version 1.2.2
 */
class ClassWithManyTags
{
    /**
     * An useless constant
     * @deprecated This constant is useless
     * @since 1.2.1
     * @todo To be removed in a later version
     */
    const USELESS_CONSTANT = 'A constant string';

    /**
     * Any property
     * @since 1.2.1
     * @var string
     * @used-by anyMethod()
     */
    protected $anyProperty = '';

    /**
     * Any method.
     * @param string $anyParam Any parameter
     * @return string Returns a useless string
     * @since 1.2.1
     * @use $anyProperty
     */
    public function anyMethod(string $anyParam = ''): string
    {
        return $anyParam ?? ($this->anyProperty ?? 'any method!');
    }

    /**
     * A deprecated method.
     * @deprecated Use instead `anyMethod()`
     * @return void
     * @see anyMethod()
     * @throws \Exception
     */
    public function aDeprecatedMethod(): void
    {
        throw new \Exception('I\'m deprecated!');
    }
}
