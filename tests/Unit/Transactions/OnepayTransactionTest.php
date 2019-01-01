<?php

namespace Tests\Unit\Transactions;

use DarkGhostHunter\TransbankApi\AbstractService;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartEmptyException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartNegativeAmountException;
use DarkGhostHunter\TransbankApi\Transactions\Item;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use PHPUnit\Framework\TestCase;

class OnepayTransactionTest extends TestCase
{

    public function testPassesItemsFromConstruct()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar', 'quantity' => 1],
                ['baz' => 'qux', 'quantity'],
                json_encode(['quux' => 'quux']),
                'NOPE'
            ]
        ]);

        $this->assertCount(1, $transaction->getItems());
        $this->assertEquals('bar', $transaction->getItem(0)->foo);
    }

    public function testUpdateItem()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $transaction->updateItem(0, [
            'foo' => 'value',
            'amount' => 1990,
            'quantity' => 2
        ]);

        $transaction->updateItem(1, [
            'foo' => 'value',
            'amount' => 1990,
            'quantity' => 0
        ]);

        $this->assertCount(2, $transaction->getItems());
        $this->assertEquals('value', $transaction->getItem(0)->foo);
        $this->assertEquals(1990, $transaction->getItem(0)->amount);
        $this->assertEquals(2, $transaction->getItem(0)->quantity);

        $this->assertNull($transaction->getItem(1));

        $this->assertEquals('quuz', $transaction->getItem(2)->quux);
        $this->assertEquals(4990, $transaction->getItem(2)->amount);
        $this->assertEquals(3, $transaction->getItem(2)->quantity);

        $this->assertFalse($transaction->updateItem(99, ['hellow']));
    }

    public function testGetItemsQuantityAttribute()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $this->assertEquals(6, $transaction->itemsQuantity);

        $transaction->addItem([
            'foo' => 'bar', 'amount' => 9990, 'quantity' => 8
        ]);

        $transaction->deleteItem(0);

        $transaction->updateItem(1, [
            'quantity' => '1'
        ]);

        $this->assertEquals(12, $transaction->itemsQuantity);
    }

    public function testToArray()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $array = $transaction->toArray();

        $this->assertEquals(29940, $array['total']);
        $this->assertEquals(6, $array['itemsQuantity']);
        $this->assertIsArray($array['items']);
        $this->assertCount(3, $array['items']);
        $this->assertArrayHasKey('externalUniqueNumber', $array);
    }

    public function testGetTotalAttribute()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $this->assertEquals(4990 * 6, $transaction->total);

        $transaction->addItem([
            'foo' => 'bar', 'amount' => 9990, 'quantity' => 8
        ]);

        $transaction->deleteItem(0);

        $transaction->updateItem(1, [
            'quantity' => '1'
        ]);

        $this->assertEquals(99880, $transaction->total);
    }

    public function testGenerateEun()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $mockService = \Mockery::mock(AbstractService::class);
        $mockService->shouldReceive('commit')
            ->andReturnUsing(function ($transaction) {
                $this->assertEquals('foo-29940', $transaction->externalUniqueNumber);
            });

        $transaction->setService($mockService);

        $transaction->generateEun(function ($transaction) {
            return 'foo-' . $transaction->total;
        });

        $transaction->commit();
    }

    public function testGenerateDefaultEun()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $mockService = \Mockery::mock(AbstractService::class);
        $mockService->shouldReceive('commit')
            ->andReturnUsing(function ($transaction) {
                $this->assertIsString($transaction->externalUniqueNumber);
                $this->assertEquals(32, strlen($transaction->externalUniqueNumber));
            });

        $transaction->setService($mockService);

        $transaction->commit();
    }

    public function testFilledEun()
    {
        $transaction = new OnepayTransaction([
            'items' => $items = [
                ['foo' => 'bar',    'amount' => 4990, 'quantity' => 1],
                ['baz' => 'qux',    'amount' => 4990, 'quantity' => 2],
                ['quux' => 'quuz',  'amount' => 4990, 'quantity' => 3],
            ]
        ]);

        $mockService = \Mockery::mock(AbstractService::class);
        $mockService->shouldReceive('commit')
            ->andReturnUsing(function ($transaction) {
                $this->assertEquals('foo', $transaction->externalUniqueNumber);
            });

        $transaction->setService($mockService);

        $transaction->externalUniqueNumber = 'foo';

        $transaction->commit();
    }

    public function testExceptionOnCommitWithEmptyCart()
    {
        $this->expectException(CartEmptyException::class);
        $transaction = new OnepayTransaction();

        $transaction->commit();
    }

    public function testExceptionOnCommitWithNegativeAmount()
    {
        $this->expectException(CartNegativeAmountException::class);

        $transaction = new OnepayTransaction([
            'items' => [
                'amount' => -9999,
                'quantity' => 1,
            ]
        ]);

        $transaction->commit();
    }
}
