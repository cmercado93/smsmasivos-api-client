<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\GetNumberMessagesSent;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class GetNumberMessagesSentTest extends TestCase
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

    public function testReturnsInteger()
    {
        $this->mock->setNextResponse(200, '42');
        $op = new GetNumberMessagesSent($this->mock);
        $result = $op->get();
        $this->assertSame(42, $result);
    }

    public function testReturnsFalseForInvalidResponse()
    {
        $this->mock->setNextResponse(200, 'invalid');
        $op = new GetNumberMessagesSent($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new GetNumberMessagesSent($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->get();
    }
}
