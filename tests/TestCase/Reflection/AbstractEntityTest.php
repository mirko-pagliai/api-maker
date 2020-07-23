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
use PhpDocMaker\TestSuite\TestCase;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use Roave\BetterReflection\Reflection\ReflectionClass;
use RuntimeException;

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
        $class = $this->getClassEntityFromTests(Animal::class);
        $expectedSummary = 'Animal abstract class.';
        $expectedDesc = 'Other animal classes have to extend this class.';
        $this->assertSame($expectedSummary, $class->getDocBlockSummaryAsString());
        $this->assertSame($expectedDesc, $class->getDocBlockDescriptionAsString());
        $this->assertSame($expectedSummary . PHP_EOL . $expectedDesc, $class->getDocBlockAsString());

        $class = $this->getClassEntityFromTests(Dog::class);
        $expectedSummary = 'Dog class.';
$expectedDesc = <<<HEREDOC
### Is it really a dog?
Yeah, this is a dog!
HEREDOC;
        $this->assertSame($expectedSummary, $class->getDocBlockSummaryAsString());
        $this->assertSame($expectedDesc, $class->getDocBlockDescriptionAsString());
        $this->assertSame($expectedSummary . PHP_EOL . $expectedDesc, $class->getDocBlockAsString());

        //Class with no DocBlock
        $this->assertSame('', $this->getClassEntityFromTests(ArrayExample::class)->getDocBlockAsString());
    }

    /**
     * Test for `getTagsByName()` method
     * @test
     */
    public function testGetTagsByName()
    {
        $tags = $this->getClassEntityFromTests(Cat::class)->getMethod('doMeow')->getTagsByName('see');
        $this->assertContainsOnlyInstancesOf(See::class, $tags);

        $entity = $this->getClassEntityFromTests(DeprecatedClassExample::class);
        $tags = $entity->getMethod('anonymousMethodWithoutDocBlock')->getTagsByName('see');
        $this->assertEmpty($tags);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid tag `@see \1`');
        $tags = $entity->getMethod('methodWithInvalidTag')->getTagsByName('see');
    }
}
