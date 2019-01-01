<?php

namespace Tests\Unit\Helpers;

use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\TransactionFactories\AbstractTransactionFactory;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{

    public function testArrayOnly()
    {
        $array = [
            'foo' => 'bar',
            'baz' => 'qux',
            'quuz' => 'quux',
        ];

        $only = Helpers::arrayOnly($array, ['baz', 'quuz']);

        $this->assertArrayHasKey('baz', $only);
        $this->assertArrayHasKey('quuz', $only);
        $this->assertArrayNotHasKey('foo', $only);
    }

    public function testClassBasename()
    {
        $name = Helpers::classBasename(self::class);

        $this->assertEquals('HelpersTest', $name);

        $name = Helpers::classBasename('anything');

        $this->assertNull($name);
    }

    public function testArrayExcept()
    {
        $array = [
            'foo' => 'bar',
            'baz' => 'qux',
            'quuz' => 'quux',
        ];

        $except = Helpers::arrayExcept($array, ['foo']);

        $this->assertArrayNotHasKey('foo', $except);
        $this->assertArrayHasKey('baz', $except);
        $this->assertArrayHasKey('quuz', $except);
    }

    public function testDirContents()
    {
        $contents = Helpers::dirContents(__DIR__);

        $this->assertIsArray($contents);
        $this->assertTrue(in_array('HelpersTest.php', $contents));
    }

    public function testIsNumericArray()
    {
        $arrayNumeric = [
            'foo', 'bar', 'baz'
        ];

        $arrayNotNumeric = [
            'foo' => 'bar',
            'baz' => 'qux',
            'quuz' => 'quux',
        ];

        $this->assertTrue(Helpers::isNumericArray($arrayNumeric));
        $this->assertFalse(Helpers::isNumericArray($arrayNotNumeric));
    }
}
