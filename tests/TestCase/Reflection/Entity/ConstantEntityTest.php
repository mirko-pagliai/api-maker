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

use ApiMaker\Reflection\Entity\ConstantEntity;
use ApiMaker\TestSuite\TestCase;
use App\Animals\Cat;

/**
 * ConstantEntityTest class
 */
class ConstantEntityTest extends TestCase
{
    /**
     * Internal method to get a `ConstantEntity` instance
     * @param string $constant Constant name
     * @param string $class Class name
     * @return ConstantEntity
     */
    protected function getConstantEntity(string $constant, string $class = Cat::class): ConstantEntity
    {
        return parent::getConstantEntity($constant, $class);
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('LEGS', (string)$this->getConstantEntity('LEGS'));
        $this->assertSame('GENUS', (string)$this->getConstantEntity('GENUS'));
    }

    /**
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame('<p>Number of legs</p>', $this->getConstantEntity('LEGS')->getDocBlockAsString());
        $this->assertSame('<p>Genus of this animal.</p>
<p>Every animal has its own genus.</p>', $this->getConstantEntity('GENUS')->getDocBlockAsString());
    }

    /**
     * Test for `getSeeTags()` method
     * @test
     */
    public function testGetSeeTags()
    {
        $this->assertSame([], $this->getConstantEntity('LEGS')->getSeeTags());
        $this->assertSame([
            'https://en.wikipedia.org/wiki/Genus',
            'https://en.wikipedia.org/wiki/Felis',
        ], $this->getConstantEntity('GENUS')->getSeeTags());
    }

    /**
     * Test for `getVisibility()` method
     * @test
     */
    public function testGetVisibility()
    {
        $this->assertSame('protected', $this->getConstantEntity('LEGS')->getVisibility());
        $this->assertSame('public', $this->getConstantEntity('GENUS')->getVisibility());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->getConstantEntity('LEGS')->isDeprecated());
        $this->assertTrue($this->getConstantEntity('GENUS')->isDeprecated());
    }
}
