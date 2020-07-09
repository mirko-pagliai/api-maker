<?php
declare(strict_types=1);

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
namespace PhpDocMaker\Reflection\Entity\Traits;

/**
 * DeprecatedTrait
 */
trait DeprecatedTrait
{
    /**
     * Returns the `@deprecated` description
     * @return string
     */
    public function getDeprecatedDescription(): string
    {
        $tag = $this->getTagsByName('deprecated');

        return $tag ? ucfirst((string)$tag[0]->getDescription()) : '';
    }

    /**
     * Returns `true` if it's deprecated (`@deprecated` tag)
     * @return bool
     */
    public function isDeprecated(): bool
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $DocBlockInstance->hasTag('deprecated') : false;
    }
}
