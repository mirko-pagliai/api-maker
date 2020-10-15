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

use Cake\Collection\Collection;
use InvalidArgumentException;
use PhpDocMaker\ErrorCatcher;
use PhpDocMaker\Reflection\Entity\TagEntity;
use PhpDocMaker\Reflection\ParentAbstractEntity;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;

/**
 * AbstractEntity class.
 *
 * This class is extended by all entities, except for `TagEntity`, which
 *  directly extends the `ParentAbstractEntity`.
 * @method ?int getStartLine() Gets the line number of code where this entity is actually declared
 */
abstract class AbstractEntity extends ParentAbstractEntity
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock|null
     */
    private $docBlock;

    /**
     * @var \Cake\Collection\Collection
     */
    private $tags;

    /**
     * Internal method to get the `DocBlock` instance
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    protected function getDocBlockInstance(): ?DocBlock
    {
        if (!$this->docBlock) {
            try {
                $this->docBlock = DocBlockFactory::createInstance()->create($this->reflectionObject);
            } catch (InvalidArgumentException $e) {
                ErrorCatcher::append($this, $e->getMessage());
            }
        }

        return $this->docBlock ?? null;
    }

    /**
     * Gets the doc block summary
     * @return string
     */
    public function getDocBlockSummaryAsString(): string
    {
        return $this->getDocBlockInstance() ? $this->getDocBlockInstance()->getSummary() : '';
    }

    /**
     * Gets the doc block description
     * @return string
     */
    public function getDocBlockDescriptionAsString(): string
    {
        return $this->getDocBlockInstance() ? (string)$this->getDocBlockInstance()->getDescription() : '';
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

        return $summary . ($description ? PHP_EOL . PHP_EOL . $description : '');
    }

    /**
     * Gets the filename in which this entity is physically declared
     * @return string|null
     */
    public function getFilename(): ?string
    {
        $object = method_exists($this->reflectionObject, 'getFileName') ? $this->reflectionObject : $this->reflectionObject->getDeclaringClass();

        return $object->getFileName();
    }

    /**
     * Gets all tags
     * @return \Cake\Collection\Collection
     */
    public function getTags(): Collection
    {
        if (!$this->tags instanceof Collection) {
            $tags = $this->getDocBlockInstance() ? $this->getDocBlockInstance()->getTags() : [];

            $this->tags = collection($tags)->map(function (Tag $tag) {
                return new TagEntity($tag);
            })->sortBy('name', SORT_ASC, SORT_STRING);
        }

        return $this->tags;
    }

    /**
     * Gets all tags grouped by name
     * @return \Cake\Collection\Collection
     */
    public function getTagsGroupedByName(): Collection
    {
        return $this->getTags()->groupBy('name');
    }

    /**
     * Gets tags by name
     * @param string $name Tags
     * @return \Cake\Collection\Collection
     */
    public function getTagsByName(string $name): Collection
    {
        return $this->getTags()->match(compact('name'));
    }

    /**
     * Returns true if the DocBlock has the tag
     * @param string $name Tag name
     * @return bool
     */
    public function hasTag(string $name): bool
    {
        return $this->getDocBlockInstance() ? $this->getDocBlockInstance()->hasTag($name) : false;
    }
}
