<?php

namespace Tests\Unit\Transactions;

use DarkGhostHunter\TransbankApi\Transactions\Item;
use DarkGhostHunter\TransbankApi\Transactions\WebpayMallTransaction;
use PHPUnit\Framework\TestCase;

class WebpayMallTransactionTest extends TestCase
{

    public function testPassesItems()
    {
        $transaction = new WebpayMallTransaction([
            'items' => $items = [
                ['foo' => 'bar'],
                ['baz' => 'qux']
            ]
        ]);

        foreach ($items as &$item) {
            $item = new Item(array_merge($item, ['sessionId' => null]));
        }

        $this->assertEquals($items, $transaction->getItems());
    }

    public function testToArray()
    {
        $transaction = new WebpayMallTransaction([
            'items' => $items = [
                ['foo' => 'bar'],
                ['baz' => 'qux']
            ]
        ]);

        $this->assertCount(2, $transaction->getItems());
        $this->assertEquals('bar', $transaction->getItem(0)->foo);
        $this->assertEquals('qux', $transaction->getItem(1)->baz);
    }

    public function testCallsOrderMethodAsItems()
    {
        $transaction = new WebpayMallTransaction([
            'items' => [
                ['foo' => 'bar'],
                ['baz' => 'qux']
            ]
        ]);

        $transaction->addOrder([
            'quux' => 'quuz'
        ]);

        $this->assertCount(3, $transaction->getOrders());
        $this->assertEquals('bar', $transaction->getOrder(0)->foo);
        $this->assertEquals('qux', $transaction->getOrder(1)->baz);
        $this->assertEquals('quuz', $transaction->getOrder(2)->quux);
    }
}
