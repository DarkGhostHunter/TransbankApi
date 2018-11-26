<?php

namespace Tests\Unit\Transactions;

use PHPUnit\Framework\TestCase;
use Transbank\Wrapper\Exceptions\Onepay\CartNegativeAmountException;

class OnepayTransactionTest extends TestCase
{

    public function testResultWithCartNegativeAmountException()
    {
        $this->expectException(CartNegativeAmountException::class);

        $this->markTestSkipped();
    }

    public function testResultWithCartEmptyException()
    {
        $this->markTestSkipped();
    }

    public function testResultWithInvalidCartItemException()
    {
        $this->markTestSkipped();
    }

}