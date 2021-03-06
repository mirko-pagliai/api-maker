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
namespace PhpDocMaker\Test;

use PhpDocMaker\ClassesExplorer;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\Reflection\Entity\FunctionEntity;
use PhpDocMaker\TestSuite\TestCase;
use Tools\Exception\NotReadableException;

/**
 * ClassesExplorerTest class
 */
class ClassesExplorerTest extends TestCase
{
    /**
     * @var \PhpDocMaker\ClassesExplorer
     */
    protected $ClassesExplorer;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->ClassesExplorer = new ClassesExplorer(TESTS . DS . 'test_app');
    }

    /**
     * Test for the construct, with a no existing path
     * @test
     */
    public function testConstructWithNoReadablePath()
    {
        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('Missing Composer autoloader on `' . DS . 'noExistingPath' . DS . 'vendor' . DS . 'autoload.php`');
        new ClassesExplorer(DS . 'noExistingPath');
    }

    /**
     * Test for the construct, on missing Composer autoloader
     * @test
     */
    public function testConstructMissingComposerAutoloader()
    {
        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('Missing Composer autoloader');
        new ClassesExplorer(TMP);
    }

    /**
     * Test for `getAllClasses()` method
     * @test
     */
    public function testGetAllClasses()
    {
        $classes = $this->ClassesExplorer->getAllClasses();
        $this->assertContainsOnlyInstancesOf(ClassEntity::class, $classes);
        $names = $classes->extract('name');
        $this->assertContains('Cake\Routing\Router', $names);
        $this->assertContains('App\Animals\Dog', $names);

        //Classes are properly sorted
        $this->assertSame('App\Animals\Animal', $names->first());
        $this->assertSame('Cake\Routing\Router', $names->last());
    }

    /**
     * Test for `getAllFunctions()` method
     * @test
     */
    public function testGetAllFunctions()
    {
        $functions = $this->ClassesExplorer->getAllFunctions();
        $names = $functions->extract('name');
        $this->assertContainsOnlyInstancesOf(FunctionEntity::class, $functions);
        $this->assertContains('a_test_function', $names);
        $this->assertContains('get_woof', $names);
    }
}
