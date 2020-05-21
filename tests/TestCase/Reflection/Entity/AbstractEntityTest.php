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
namespace ApiMaker\Test\Reflection\Entity;

use ApiMaker\TestSuite\TestCase;
use App\Animals\Cat;
use BadMethodCallException;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * AbstractEntityTest class
 */
class AbstractEntityTest extends TestCase
{
    /**
     * Test for `__call()` magic method
     * @test
     */
    public function testCall()
    {
        $entity = $this->getClassEntity(Cat::class);
        $this->assertNotEmpty($entity->getConstant('LEGS'));
        $this->assertNotEmpty($entity->getImmediateReflectionConstants());

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method ' . ReflectionClass::class . '::noExistingMethod() does not exist');
        $entity->noExistingMethod();
    }

    /**
     * Test for `getDocBlockAsString()`, `getDocBlockDescriptionAsString()` and
     *  `getDocBlockSummaryAsString()` methods
     * @test
     */
    public function testGetDocBlockMethods()
    {
        $class = <<<HEREDOC
/**
 * Class summary.
 *
 * Class description.
 */
class MyClass {
}
HEREDOC;
        $this->assertSame('<p>Class summary.</p>', $this->getClassEntityFromString($class)->getDocBlockSummaryAsString());
        $this->assertSame('<p>Class description.</p>', $this->getClassEntityFromString($class)->getDocBlockDescriptionAsString());
        $this->assertSame('<p>Class summary.</p>
<p>Class description.</p>', $this->getClassEntityFromString($class)->getDocBlockAsString());

        $class = <<<HEREDOC
/**
 * Class summary.
 *
 * ### Header
 * Description
 */
class MyClass {
}
HEREDOC;
        $this->assertSame('<h3>Header</h3>
<p>Description</p>', $this->getClassEntityFromString($class)->getDocBlockDescriptionAsString());
        $this->assertSame('<p>Class summary.</p>
<h3>Header</h3>
<p>Description</p>', $this->getClassEntityFromString($class)->getDocBlockAsString());
    }
}
