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
namespace PhpDocMaker\Test\Reflection;

use App\Animals\Animal;
use App\Animals\Cat;
use App\Animals\Dog;
use App\ArrayExample;
use App\DeprecatedClassExample;
use BadMethodCallException;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\Reflection\Entity\TagEntity;
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
        $entity = ClassEntity::createFromName(Cat::class);
        $this->assertFalse(method_exists($entity, 'getImmediateReflectionConstants'));
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
        $class = ClassEntity::createFromName(Animal::class);
        $expectedSummary = 'Animal abstract class.';
        $expectedDesc = 'Other animal classes have to extend this class.';
        $this->assertSame($expectedSummary, $class->getDocBlockSummaryAsString());
        $this->assertSame($expectedDesc, $class->getDocBlockDescriptionAsString());
        $this->assertSame($expectedSummary . PHP_EOL . $expectedDesc, $class->getDocBlockAsString());

        $class = ClassEntity::createFromName(Dog::class);
        $expectedSummary = 'Dog class.';
        $expectedDesc = <<<HEREDOC
### Is it really a dog?
Yeah, this is a dog!
HEREDOC;
        $this->assertSame($expectedSummary, $class->getDocBlockSummaryAsString());
        $this->assertSame($expectedDesc, $class->getDocBlockDescriptionAsString());
        $this->assertSame($expectedSummary . PHP_EOL . $expectedDesc, $class->getDocBlockAsString());

        //Class with no DocBlock
        $this->assertSame('', ClassEntity::createFromName(ArrayExample::class)->getDocBlockAsString());
    }

    /**
     * Test for `getTags()` method
     * @test
     */
    public function testGetTags()
    {
        $tags = ClassEntity::createFromName(Cat::class)->getMethod('doMeow')->getTags();
        $this->assertNotEmpty($tags);
        $this->assertContainsOnlyInstancesOf(TagEntity::class, $tags);
    }

    /**
     * Test for `getTagsByName()` method
     * @test
     */
    public function testGetTagsByName()
    {
        $tags = ClassEntity::createFromName(Cat::class)->getMethod('doMeow')->getTagsByName('link');
        $this->assertFalse($tags->isEmpty());
        $this->assertContainsOnlyInstancesOf(TagEntity::class, $tags);

        $entity = ClassEntity::createFromName(DeprecatedClassExample::class);
        $tags = $entity->getMethod('anonymousMethodWithoutDocBlock')->getTagsByName('link');
        $this->assertTrue($tags->isEmpty());
    }

    /**
     * Test for `hasTag()` method
     * @test
     */
    public function testHasTag()
    {
        $method = ClassEntity::createFromName(Cat::class)->getMethod('doMeow');
        $this->assertTrue($method->hasTag('link'));
        $this->assertFalse($method->hasTag('use'));

        //This method has no DocBlock
        $method = ClassEntity::createFromName(DeprecatedClassExample::class)->getMethod('anonymousMethodWithoutDocBlock');
        $this->assertFalse($method->hasTag('link'));
    }
}
