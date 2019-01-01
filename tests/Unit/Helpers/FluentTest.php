<?php

namespace Tests\Unit\Helpers;

use BadMethodCallException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use PHPUnit\Framework\TestCase;

class FluentTest extends TestCase
{

    public function test__construct()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertEquals('bar', $fluent->foo);
        $this->assertEquals('bar', $fluent['foo']);
    }

    public function testToArray()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertIsArray((array)$fluent);
    }

    public function test__set()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent->baz = 'qux';

        $this->assertEquals('qux', $fluent->baz);
        $this->assertEquals('bar', $fluent->foo);
    }

    public function test__toString()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertIsString((string)$fluent);
    }

    public function test__get()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertIsString('bar', $fluent->foo);
    }

    public function testOffsetSet()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent['baz'] = 'qux';

        $this->assertEquals('qux', $fluent->baz);
    }

    public function test__isset()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertTrue(isset($fluent->foo));
        $this->assertFalse(isset($fluent->baz));
    }

    public function testSetAttributes()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent->setAttributes([
            'baz' => 'qux'
        ]);

        $this->assertEquals('qux', $fluent->baz);
        $this->assertNull($fluent->foo);
    }

    public function testOffsetExists()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertTrue(isset($fluent['foo']));
        $this->assertFalse(isset($fluent['baz']));
    }

    public function testOffsetGet()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertEquals('bar', $fluent['foo']);
    }

    public function testGet()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertEquals('bar', $fluent->get('foo'));
    }

    public function testSet()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent->set('baz', 'qux');

        $this->assertEquals('bar', $fluent->get('foo'));
        $this->assertEquals('qux', $fluent->get('baz'));
    }

    public function testSetAttributeDynamically()
    {
        $fluent = new class ([
            'foo' => 'bar'
        ]) extends Fluent {
            public function setBazAttribute($value)
            {
                $this->attributes['baz'] = 'changedValue';
            }
        };

        $fluent->baz = 'anything';

        $this->assertEquals('changedValue', $fluent->get('baz'));
    }

    public function testGetAttributeDynamically()
    {
        $fluent = new class ([
            'foo' => 'bar'
        ]) extends Fluent {
            public function getBazAttribute()
            {
                return 'changedValue';
            }
        };

        $this->baz = 'anything';

        $this->assertEquals('changedValue', $fluent->baz);
    }

    public function test__call()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent->baz('qux');

        $this->assertEquals('bar', $fluent->foo);
        $this->assertEquals('qux', $fluent->baz);
    }


    public function testExceptionOnInvalidCall()
    {
        $this->expectException(BadMethodCallException::class);

        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent->doesnotexists();
    }

    public function testFromJson()
    {
        $fluent = Fluent::fromJson(json_encode(['foo' => 'bar']));

        $this->assertInstanceOf(Fluent::class, $fluent);
        $this->assertEquals('bar', $fluent->foo);
    }

    public function test__unset()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        unset($fluent->foo);

        $this->assertNull($fluent->foo);
    }

    public function testToJson()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertJson($fluent->toJson());
    }

    public function testJsonSerialize()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertJson(json_encode($fluent));
    }

    public function testOffsetUnset()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        unset($fluent['foo']);

        $this->assertNull($fluent->foo);
    }

    public function testGetAttributes()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $this->assertEquals(['foo' => 'bar'], $fluent->getAttributes());
    }

    public function testMergeAttributes()
    {
        $fluent = new Fluent([
            'foo' => 'bar'
        ]);

        $fluent->mergeAttributes([
            'baz' => 'quz'
        ]);

        $this->assertCount(2, $fluent->getAttributes());

        $this->assertEquals('quz', $fluent->baz);
        $this->assertEquals('bar', $fluent->foo);
    }
}
