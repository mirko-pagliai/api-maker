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

use PhpDocMaker\Reflection\Entity\ClassEntity;

/**
 * GetDeclaringClassTrait
 */
trait GetDeclaringClassTrait
{
    /**
     * Gets the class entity that declares this object
     * @return \PhpDocMaker\Reflection\Entity\ClassEntity
     */
    public function getDeclaringClass(): ClassEntity
    {
        return new ClassEntity($this->reflectionObject->getDeclaringClass());
    }
}
