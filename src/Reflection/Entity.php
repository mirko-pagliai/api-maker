<?php
declare(strict_types=1);

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
namespace ApiMaker\Reflection;

use BadMethodCallException;
use League\CommonMark\CommonMarkConverter;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;

/**
 * Entity abstract class
 */
abstract class Entity
{
    /**
     * `__call()` magic method.
     *
     * It allows access to the methods of the reflected object.
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this->reflectionObject, $name)) {
            throw new BadMethodCallException(sprintf('Method %s::%s() does not exist', get_class($this->reflectionObject), $name));
        }

        return call_user_func_array([$this->reflectionObject, $name], $arguments);
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return $this->reflectionObject->getName();
    }

    /**
     * Internal method to get the `DocBlock` instance
     * @return \phpDocumentor\Reflection\DocBlock
     */
    protected function getDocBlockInstance(): DocBlock
    {
        return DocBlockFactory::createInstance()->create($this->reflectionObject);
    }

    /**
     * Gets the doc block summary
     * @return string
     */
    public function getDocBlockSummaryAsString(): string
    {
        $summary = $this->getDocBlockInstance()->getSummary();

        return trim((new CommonMarkConverter())->convertToHtml($summary));
    }

    /**
     * Gets the doc block description
     * @return string
     */
    public function getDocBlockDescriptionAsString(): string
    {
        $description = $this->getDocBlockInstance()->getDescription()->getBodyTemplate();

        return trim((new CommonMarkConverter())->convertToHtml($description));
    }

    /**
     * Gets the doc block as string
     * @return string
     */
    public function getDocBlockAsString(): string
    {
        $summary = $this->getDocBlockSummaryAsString();
        $description = $this->getDocBlockDescriptionAsString();

        return $summary . ($description ? PHP_EOL . $description : '');
    }
}
