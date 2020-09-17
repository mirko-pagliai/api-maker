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
namespace PhpDocMaker\Reflection;

use Exception;
use PhpDocMaker\Reflection\Entity\TagEntity;
use PhpDocMaker\Reflection\ParentAbstractEntity;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;

/**
 * AbstractEntity class
 */
abstract class AbstractEntity extends ParentAbstractEntity
{
    /**
     * Internal method to get the `DocBlock` instance
     * @param \Roave\BetterReflection\Reflection\Reflection|\Roave\BetterReflection\Reflection\ReflectionFunctionAbstract|null $reflectionObject A `Reflection` object
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    protected function getDocBlockInstance($reflectionObject = null): ?DocBlock
    {
        try {
            return DocBlockFactory::createInstance()->create($reflectionObject ?: $this->reflectionObject);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Gets the doc block summary
     * @return string
     */
    public function getDocBlockSummaryAsString(): string
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $DocBlockInstance->getSummary() : '';
    }

    /**
     * Gets the doc block description
     * @return string
     */
    public function getDocBlockDescriptionAsString(): string
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $DocBlockInstance->getDescription()->render() : '';
    }

    /**
     * Gets the doc block as string
     * @return string
     */
    public function getDocBlockAsString(): string
    {
        $summary = $this->getDocBlockSummaryAsString();
        if (!$summary) {
            return '';
        }

        $description = $this->getDocBlockDescriptionAsString();

        return $summary . ($description ? PHP_EOL . $description : '');
    }

    /**
     * Gets tags by name
     * @param string $name Tags
     * @return array
     */
    public function getTagsByName($name): array
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? array_map(function (Tag $tag) {
            return new TagEntity($tag);
        }, $DocBlockInstance->getTagsByName($name)) : [];
    }

    /**
     * Returns true if the DocBlock has the tag
     * @param string $name Tag name
     * @return bool
     */
    public function hasTag(string $name): bool
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $DocBlockInstance->hasTag($name) : false;
    }
}
