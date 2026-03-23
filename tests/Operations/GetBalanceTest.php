<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\GetBalance;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class GetBalanceTest extends TestCase
{
    protected $mock;

    protected function setUp(): void
    {
        Credentials::setApiKey('TEST_KEY');
        $this->mock = new MockHttpRequest();
    }

    protected function tearDown(): void
    {
        Credentials::reset();
    }

    public function testGetBalanceReturnsInteger()
    {
        $this->mock->setNextResponse(200, '150');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(150, $result);
    }

    public function testGetBalanceZero()
    {
        $this->mock->setNextResponse(200, '0');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(0, $result);
    }

    public function testGetBalanceInvalidResponseReturnsMinusOne()
    {
        $this->mock->setNextResponse(200, 'not_a_number');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(-1, $result);
    }

    public function testGetBalanceHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new GetBalance($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->get();
    }

    public function testGetBalanceRejectsScientificNotation()
    {
        $this->mock->setNextResponse(200, '1e5');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(-1, $result);
    }

    public function testGetBalanceTrimsWhitespace()
    {
        $this->mock->setNextResponse(200, '  150  ');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(150, $result);
    }

    public function testGetBalanceRejectsNegativeNumber()
    {
        $this->mock->setNextResponse(200, '-1');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(-1, $result);
    }

    public function testGetBalanceRejectsDecimal()
    {
        $this->mock->setNextResponse(200, '150.50');
        $op = new GetBalance($this->mock);
        $result = $op->get();
        $this->assertSame(-1, $result);
    }
}
