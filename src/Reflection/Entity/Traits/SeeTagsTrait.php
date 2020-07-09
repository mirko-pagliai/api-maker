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

use phpDocumentor\Reflection\DocBlock\Tags\See;

/**
 * SeeTagsTrait
 */
trait SeeTagsTrait
{
    /**
     * Returns `@see` tags
     * @return array
     */
    public function getSeeTags(): array
    {
        return array_map(function (See $see) {
            $see = (string)$see->getReference();

            return is_url($see) || strpos('::', $see) !== false ? $see : ltrim($see, '\\');
        }, $this->getTagsByName('see'));
    }
}
