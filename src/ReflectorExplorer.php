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
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

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
     * @var \Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator
     */
    protected $DirectoriesSourceLocator;

    /**
     * @var \Roave\BetterReflection\Reflector\FunctionReflector
     */
    protected $FunctionReflector;

    /**
     * @var array
     */
    protected $paths;

    /**
     * Construct
     * @param array $paths Array of paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Internal method to get a `ClassReflector` instance
     * @return \Roave\BetterReflection\Reflector\ClassReflector
     * @uses getDirectoriesSourceLocator()
     */
    protected function getClassReflector(): ClassReflector
    {
        if (!$this->ClassReflector) {
            $this->ClassReflector = new ClassReflector($this->getDirectoriesSourceLocator());
        }

        return $this->ClassReflector;
    }

    /**
     * Internal method to get a `DirectoriesSourceLocator` instance
     * @return \Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator
     */
    protected function getDirectoriesSourceLocator(): DirectoriesSourceLocator
    {
        if (!$this->DirectoriesSourceLocator) {
            $astLocator = (new BetterReflection())->astLocator();
            $this->DirectoriesSourceLocator = new DirectoriesSourceLocator($this->paths, $astLocator);
        }

        return $this->DirectoriesSourceLocator;
    }

    /**
     * Internal method to get a `FunctionReflector` instance
     * @return \Roave\BetterReflection\Reflector\FunctionReflector
     * @uses getClassReflector()
     * @uses getDirectoriesSourceLocator()
     */
    protected function getFunctionReflector(): FunctionReflector
    {
        if (!$this->FunctionReflector) {
            $this->FunctionReflector = new FunctionReflector($this->getDirectoriesSourceLocator(), $this->getClassReflector());
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
        return array_map(function (ReflectionClass $class) {
            return new ClassEntity($class);
        }, $this->getClassReflector()->getAllClasses());
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
