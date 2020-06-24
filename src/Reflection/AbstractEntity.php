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
namespace ApiMaker\Reflection;

use BadMethodCallException;
use League\CommonMark\CommonMarkConverter;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;

/**
 * AbstractEntity class
 */
abstract class AbstractEntity
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
        return $this->getName();
    }

    /**
     * Internal method to get the `DocBlock` instance
     * @param \Roave\BetterReflection\Reflection\Reflection|\Roave\BetterReflection\Reflection\ReflectionFunctionAbstract|null $reflectionObject A `Reflection` object
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    protected function getDocBlockInstance($reflectionObject = null): ?DocBlock
    {
        try {
            return DocBlockFactory::createInstance()->create($reflectionObject ?: $this->reflectionObject);
        } catch (\Exception $e) {
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

        return $DocBlockInstance ? $this->toHtml($DocBlockInstance->getSummary()) : '';
    }

    /**
     * Gets the doc block description
     * @return string
     */
    public function getDocBlockDescriptionAsString(): string
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $this->toHtml($DocBlockInstance->getDescription()->getBodyTemplate()) : '';
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
     * Gets the class name
     * @return string
     */
    public function getName(): string
    {
        return $this->reflectionObject->getName();
    }

    /**
     * Internal method to convert Markdown to Html
     * @param string $string Markdown string
     * @return string Html string
     */
    protected function toHtml($string): string
    {
        return trim((new CommonMarkConverter())->convertToHtml($string));
    }
}
