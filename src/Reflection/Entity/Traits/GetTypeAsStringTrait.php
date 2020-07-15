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
 * GetTypeAsStringTrait
 */
trait GetTypeAsStringTrait
{
    /**
     * Returns the type as string
     * @return string
     */
    public function getTypeAsString(): string
    {
        if (!method_exists($this->reflectionObject, 'hasType') || !$this->reflectionObject->hasType()) {
            return implode('|', array_map(function (string $type) {
                return ltrim($type, '\\');
            }, $this->reflectionObject->getDocBlockTypeStrings()));
        }

        $mapping = ['int' => 'integer', 'bool' => 'boolean'];
        $originalType = (string)$this->reflectionObject->getType();
        $type = $mapping[$originalType] ?? $originalType;

        return $this->reflectionObject->allowsNull() ? $type . '|null' : $type;
    }
}
