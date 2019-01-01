<?php

namespace Tests\Unit\Transactions\Concerns;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\Transactions\Concerns\HasSecrets;
use PHPUnit\Framework\TestCase;

class HasSecretsTest extends TestCase
{

    /** @var Fluent&HasSecrets */
    protected $fluent;

    protected function setUp()
    {
        $this->fluent = new class extends Fluent {
            use HasSecrets;
            protected $attributes = [
                'foo' => 'bar',
                'baz' => 'qux'
            ];

            public function toArray()
            {
                if ($this->hideSecrets) {
                    return Helpers::arrayExcept($this->attributes, ['foo']);
                }
                return $this->attributes;
            }
        };
    }

    public function testHideSecrets()
    {
        $this->fluent->hideSecrets();
        $this->assertTrue($this->fluent->isHidingSecrets());
        $this->fluent->showSecrets();
        $this->assertFalse($this->fluent->isHidingSecrets());
    }

    public function testManipulatesSerialization()
    {
        $this->fluent->hideSecrets();

        $this->assertEquals('bar', $this->fluent->foo);
        $this->assertEquals('qux', $this->fluent->baz);
        $this->assertStringNotContainsString('bar', $this->fluent->toJson());
        $this->assertStringContainsString('qux', $this->fluent->toJson());
        $this->assertArrayNotHasKey('foo', $this->fluent->toArray());
        $this->assertArrayHasKey('baz', $this->fluent->toArray());

        $this->fluent->showSecrets();

        $this->assertEquals('bar', $this->fluent->foo);
        $this->assertEquals('qux', $this->fluent->baz);
        $this->assertStringContainsString('bar', $this->fluent->toJson());
        $this->assertStringContainsString('qux', $this->fluent->toJson());
        $this->assertArrayHasKey('foo', $this->fluent->toArray());
        $this->assertArrayHasKey('baz', $this->fluent->toArray());
    }
}
