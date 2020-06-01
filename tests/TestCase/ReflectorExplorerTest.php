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
namespace ApiMaker\Test;

use ApiMaker\Reflection\Entity\ClassEntity;
use ApiMaker\Reflection\Entity\FunctionEntity;
use ApiMaker\ReflectorExplorer;
use Tools\Exception\NotReadableException;
use Tools\TestSuite\TestCase;

/**
 * ReflectorExplorerTest class
 */
class ReflectorExplorerTest extends TestCase
{
    /**
     * @var \ApiMaker\ReflectorExplorer
     */
    protected $ReflectorExplorer;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->ReflectorExplorer = new ReflectorExplorer([TESTS . DS . 'test_app']);
    }

    /**
     * Test for the construct, with a no existing path
     * @test
     */
    public function testConstructWithNoReadablePath()
    {
        $this->expectException(NotReadableException::class);
        new ReflectorExplorer([TESTS . DS . 'test_app', DS . 'noExistingPath']);
    }

    /**
     * Test for `getAllClasses()` method
     * @test
     */
    public function testGetAllClasses()
    {
        $classes = $this->ReflectorExplorer->getAllClasses();
        $names = objects_map($classes, 'getName');
        $this->assertContainsOnlyInstancesOf(ClassEntity::class, $classes);
        $this->assertContains('Cake\Routing\Router', $names);
        $this->assertContains('App\Animals\Dog', $names);
    }

    /**
     * Test for `getAllFunctions()` method
     * @test
     */
    public function testGetAllFunctions()
    {
        $functions = $this->ReflectorExplorer->getAllFunctions();
        $names = objects_map($functions, 'getName');
        $this->assertContainsOnlyInstancesOf(FunctionEntity::class, $functions);
        $this->assertContains('a_test_function', $names);
        $this->assertContains('get_woof', $names);
    }
}
