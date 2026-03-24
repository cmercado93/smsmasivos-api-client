<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Smsmasivos;
use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class SmsmasivosTest extends TestCase
{
    protected $mock;

    protected function setUp(): void
    {
        Credentials::setApiKey('TEST_KEY');
        $this->mock = new MockHttpRequest();
        Smsmasivos::setHttpRequest($this->mock);
    }

    protected function tearDown(): void
    {
        Credentials::reset();
        Smsmasivos::setHttpRequest(null);
    }

    public function testSendMessage()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $result = Smsmasivos::sendMessage('1234567890', 'Hello');
        $this->assertTrue($result);
    }

    public function testSendMessagesInBlock()
    {
        $this->mock->setNextResponse(200, 'OK');
        $result = Smsmasivos::sendMessagesInBlock(array(
            'messages' => array(
                array('phone_number' => '1234567890', 'message' => 'Hello'),
            ),
        ));
        $this->assertTrue($result);
    }

    public function testCheckMessageBlockSent()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $result = Smsmasivos::checkMessageBlockSent('Ab123');
        $this->assertFalse($result);
    }

    public function testReceiveMessages()
    {
        $this->mock->setNextResponse(200, '');
        $result = Smsmasivos::receiveMessages();
        $this->assertIsArray($result);
    }

    public function testGetBalance()
    {
        $this->mock->setNextResponse(200, '100');
        $result = Smsmasivos::getBalance();
        $this->assertSame(100, $result);
    }

    public function testGetPackageExpiration()
    {
        $this->mock->setNextResponse(200, '2026-12-31');
        $result = Smsmasivos::getPackageExpiration();
        $this->assertInstanceOf('\DateTime', $result);
    }

    public function testGetNumberMessagesSent()
    {
        $this->mock->setNextResponse(200, '42');
        $result = Smsmasivos::getNumberMessagesSent();
        $this->assertSame(42, $result);
    }

    public function testGetCurrentDateServer()
    {
        $this->mock->setNextResponse(200, '2026-03-23 15:30:00');
        $result = Smsmasivos::getCurrentDateServer();
        $this->assertInstanceOf('\DateTime', $result);
    }

    public function testSetHttpRequestNull()
    {
        Smsmasivos::setHttpRequest(null);
        $this->mock->setNextResponse(200, '0;OK');
        Smsmasivos::setHttpRequest($this->mock);
        $result = Smsmasivos::sendMessage('1234567890', 'Hello');
        $this->assertTrue($result);
    }

    public function testSendMessagesInBlockDefaultConfigs()
    {
        $this->mock->setNextResponse(200, 'OK');
        $result = Smsmasivos::sendMessagesInBlock(array(
            'messages' => array(
                array('phone_number' => '1234567890', 'message' => 'Hello'),
            ),
        ));
        $this->assertTrue($result);
    }
}
