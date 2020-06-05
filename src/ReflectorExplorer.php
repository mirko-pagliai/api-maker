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
namespace ApiMaker;


use ApiMaker\Reflection\Entity\ClassEntity;
use ApiMaker\Reflection\Entity\FunctionEntity;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\FileIteratorSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use Symfony\Component\Finder\Finder;

/**
 * ReflectorExplorer.
 *
 * It finds and gets all classes and all functions in the desired path.
 */
class ReflectorExplorer
{
    /**
     * @var \Roave\BetterReflection\Reflector\ClassReflector
     */
    protected $ClassReflector;

    /**
     * @var \Roave\BetterReflection\SourceLocator\Type\SourceLocator
     */
    protected $SourceLocator;

    /**
     * @var \Roave\BetterReflection\Reflector\FunctionReflector
     */
    protected $FunctionReflector;

    /**
     * Construct
     * @param string $path Source path
     * @throws \Tools\Exception\NotReadableException
     */
    public function __construct(string $path)
    {
        is_readable_or_fail($path);

        $astLocator = (new BetterReflection())->astLocator();
        $finder = new Finder();
        $finder->in($path)->files()->name('*.php')->notPath(['tests', 'vendor']);
        $classLoader = require add_slash_term($path) . 'vendor' . DS . 'autoload.php';

        $this->SourceLocator = new AggregateSourceLocator([
            new FileIteratorSourceLocator($finder->getIterator(), $astLocator),
            new ComposerSourceLocator($classLoader, $astLocator),
            new PhpInternalSourceLocator($astLocator, new ReflectionSourceStubber())
        ]);
    }

    /**
     * Internal method to get a `ClassReflector` instance
     * @return \Roave\BetterReflection\Reflector\ClassReflector
     */
    protected function getClassReflector(): ClassReflector
    {
        if (!$this->ClassReflector) {
            $this->ClassReflector = new ClassReflector($this->SourceLocator);
        }

        return $this->ClassReflector;
    }

    /**
     * Internal method to get a `FunctionReflector` instance
     * @return \Roave\BetterReflection\Reflector\FunctionReflector
     * @uses getClassReflector()
     */
    protected function getFunctionReflector(): FunctionReflector
    {
        if (!$this->FunctionReflector) {
            $this->FunctionReflector = new FunctionReflector($this->SourceLocator, $this->getClassReflector());
        }

        return $this->FunctionReflector;
    }

    /**
     * Gets all classes found in the path
     * @return array Array of `ClassEntity` instances
     * @uses getClassReflector()
     */
    public function getAllClasses(): array
    {
        $classes = array_map(function (ReflectionClass $class) {
            return new ClassEntity($class);
        }, $this->getClassReflector()->getAllClasses());

        usort($classes, function (ClassEntity $a, ClassEntity $b) {
            return strcmp($a->getName(), $b->getName());
        });

        return $classes;
    }

    /**
     * Gets all functions found in the path
     * @return array Array of `FunctionEntity` instances
     * @uses getFunctionReflector()
     */
    public function getAllFunctions(): array
    {
        return array_map(function (ReflectionFunction $function) {
            return new FunctionEntity($function);
        }, $this->getFunctionReflector()->getAllFunctions());
    }
}
