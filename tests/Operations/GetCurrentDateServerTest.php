<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\GetCurrentDateServer;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class GetCurrentDateServerTest extends TestCase
{
    protected $mock;

    protected function setUp(): void
    {
        $this->mock = new MockHttpRequest();
    }

    public function testReturnsDateTime()
    {
        $this->mock->setNextResponse(200, '2026-03-23 15:30:00');
        $op = new GetCurrentDateServer($this->mock);
        $result = $op->get();
        $this->assertInstanceOf('\DateTime', $result);
    }

    public function testReturnsFalseForInvalidDate()
    {
        $this->mock->setNextResponse(200, 'not-a-date');
        $op = new GetCurrentDateServer($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new GetCurrentDateServer($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->get();
    }

    public function testDoesNotRequireCredentials()
    {
        Credentials::reset();
        $this->mock->setNextResponse(200, '2026-03-23 15:30:00');
        $op = new GetCurrentDateServer($this->mock);
        $result = $op->get();
        $this->assertInstanceOf('\DateTime', $result);
    }
}
