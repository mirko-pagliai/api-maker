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
namespace PhpDocMaker\Test\Reflection\Entity;

use App\Animals\Animal;
use App\Animals\Cat;
use App\Animals\Dog;
use App\ArrayExample;
use BadMethodCallException;
use PhpDocMaker\TestSuite\TestCase;
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
        $entity = $this->getClassEntityFromTests(Cat::class);
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
        $class = $this->getClassEntityFromTests(Animal::class);
        $this->assertSame('<p>Animal abstract class.</p>', $class->getDocBlockSummaryAsString());
        $this->assertSame('<p>Other animal classes have to extend this class.</p>', $class->getDocBlockDescriptionAsString());
        $this->assertSame('<p>Animal abstract class.</p>' . PHP_EOL . '<p>Other animal classes have to extend this class.</p>', $class->getDocBlockAsString());

        $class = $this->getClassEntityFromTests(Dog::class);
        $expected = <<<HEREDOC
<h3>Is it really a dog?</h3>
<p>Yeah, this is a dog!</p>
HEREDOC;
        $this->assertSame($expected, $class->getDocBlockDescriptionAsString());
        $this->assertSame('<p>Dog class.</p>' . PHP_EOL . $expected, $class->getDocBlockAsString());

        //Class with no DocBlock
        $this->assertSame('', $this->getClassEntityFromTests(ArrayExample::class)->getDocBlockAsString());
    }
}
