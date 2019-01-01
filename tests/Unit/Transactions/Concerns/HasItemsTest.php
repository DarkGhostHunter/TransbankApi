<?php

namespace Tests\Unit\Transactions\Concerns;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction;
use DarkGhostHunter\TransbankApi\Transactions\Concerns\HasItems;
use DarkGhostHunter\TransbankApi\Transactions\Item;
use PHPUnit\Framework\TestCase;

class HasItemsTest extends TestCase
{

    /** @var AbstractTransaction&HasItems */
    protected $transaction;

    protected function setUp()
    {
        $this->transaction = new class extends AbstractTransaction { use HasItems; };
    }

    public function testCountItems()
    {
        $this->transaction->addItems([
            ['foo' => 'bar'],
            ['baz' => 'qux']
        ]);

        $this->assertEquals(2, $this->transaction->countItems());
    }

    public function testGetItems()
    {
        $this->transaction->addItems([
            ['foo' => 'bar'],
            ['baz' => 'qux']
        ]);

        $this->assertIsArray($this->transaction->getItems());

        $this->assertEquals('bar', $this->transaction->getItems()[0]->foo);
        $this->assertEquals('qux', $this->transaction->getItems()[1]->baz);
    }

    public function testDeleteItemByDescription()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $this->transaction->deleteItemByDescription('test-description');

        $this->assertNull($this->transaction->getItem(1));
        $this->assertCount(2, $this->transaction->getItems());

        $this->assertFalse($this->transaction->deleteItemByDescription('dontdeletethis'));
    }

    public function testAddItem()
    {
        $this->transaction->addItem(
            ['foo' => 'bar', 'description' => 'rofl']
        );

        $this->transaction->addItem(
            json_encode(['baz' => 'qux'])
        );

        $this->transaction->addItem(
            'NOPE'
        );

        $item = $this->transaction->getItem(0);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('bar', $item->foo);

        $item = $this->transaction->getItem(1);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('qux', $item->baz);

        $this->assertNull($this->transaction->getItem(2));
    }

    public function testAddItems()
    {
        $this->transaction->addItems([
            ['foo' => 'bar'],
            ['baz' => 'qux']
        ]);

        $item = $this->transaction->getItem(0);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('bar', $item->foo);

        $item = $this->transaction->getItem(1);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('qux', $item->baz);

        $this->assertCount(2, $this->transaction->getItems());
    }

    public function testDeleteItem()
    {
        $this->transaction->addItems([
            ['foo' => 'bar'],
            ['baz' => 'qux']
        ]);

        $this->transaction->deleteItem(1);

        $this->assertCount(1, $this->transaction->getItems());
        $this->assertNull($this->transaction->getItem(1));
        $this->assertNotNull($this->transaction->getItem(0));

        $this->assertFalse($this->transaction->deleteItem(999));
    }

    public function testUpdateItem()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'replacethis' => 'yes'],
            ['baz' => 'qux']
        ]);

        $this->transaction->updateItem(0, [
            'test' => 'value',
            'replacethis' => 'no'
        ]);

        $this->assertEquals('value', $this->transaction->getItem(0)->test);
        $this->assertEquals('bar', $this->transaction->getItem(0)->foo);
        $this->assertEquals('no', $this->transaction->getItem(0)->replacethis);

        $this->assertFalse($this->transaction->updateItem(999, ['dontupdate']));
    }

    public function testReindexItems()
    {
        $this->transaction->addItems([
            ['foo' => 'bar'],
            ['baz' => 'qux']
        ]);

        $this->transaction->deleteItem(0);

        $this->transaction->reindexItems();

        $this->assertCount(1, $this->transaction->getItems());
        $this->assertNull($this->transaction->getItem(1));
        $this->assertNotNull($this->transaction->getItem(0));

    }

    public function testGetItemByDescription()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $item = $this->transaction->getItemByDescription('rofl');

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('bar', $item->foo);

        $this->assertNull($this->transaction->getItemByDescription('dontfindthis'));
    }

    public function testClearItems()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $this->transaction->clearItems();

        $this->assertCount(0, $this->transaction->getItems());
    }

    public function testGetItem()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $item = $this->transaction->getItem(1);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('qux', $item->baz);
    }

    public function testGetItemKeyByDescription()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $item = $this->transaction->getItemKeyByDescription('test-description');

        $this->assertEquals(1, $item);

        $this->assertNull($this->transaction->getItemKeyByDescription('dontfindthis'));
    }

    public function testGetItemsAttribute()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $items = $this->transaction->items;

        $this->assertIsArray($items);
        $this->assertCount(3, $items);
    }

    public function testReplaceItem()
    {
        $this->transaction->addItems([
            ['foo' => 'bar', 'description' => 'rofl'],
            ['baz' => 'qux', 'description' => 'test-description'],
            ['quux' => 'quuz', 'description' => 'test-lol'],
        ]);

        $item = $this->transaction->replaceItem(2, [
            'test' => 'value'
        ]);

        $this->assertTrue($item);
        $this->assertInstanceOf(Item::class, $this->transaction->getItem(2));
        $this->assertEquals('value', $this->transaction->getItem(2)->test);

        $item = $this->transaction->replaceItem(3, [
            'test' => 'value'
        ]);

        $this->assertFalse($item);
    }
}
