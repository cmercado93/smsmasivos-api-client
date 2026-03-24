<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\ReceiveMessages;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class ReceiveMessagesTest extends TestCase
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

    public function testReceiveEmptyResponse()
    {
        $this->mock->setNextResponse(200, '');
        $op = new ReceiveMessages($this->mock);
        $result = $op->receive();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testReceiveWithInternalId()
    {
        $response = "1234567890\tHello world\t2026-01-15 10:30:00\t999\tAb123";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
        $this->assertEquals('1234567890', $result[0]['phone_number']);
        $this->assertEquals('Hello world', $result[0]['message']);
        $this->assertEquals('999', $result[0]['smsmasivos_id']);
        $this->assertEquals('Ab123', $result[0]['internal_id']);
    }

    public function testReceiveWithoutInternalId()
    {
        $response = "1234567890\tHello\t2026-01-15 10:30:00\t999";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('include_internal_id' => false, 'api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
        $this->assertArrayNotHasKey('internal_id', $result[0]);
    }

    public function testReceiveMultipleMessages()
    {
        $response = "1234567890\tHello\t2026-01-15 10:30:00\t999\tAb1\n0987654321\tWorld\t2026-01-15 11:00:00\t1000\tAb2";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(2, $result);
    }

    public function testReceiveWith8DigitPhone()
    {
        $response = "12345678\tHello\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
        $this->assertEquals('12345678', $result[0]['phone_number']);
    }

    public function testReceiveMessageWithComma()
    {
        $response = "1234567890\tHello, world\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
        $this->assertStringContainsString(',', $result[0]['message']);
    }

    public function testReceiveMessageWithSpecialChars()
    {
        $response = "1234567890\tHola que tal! como estas?\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
    }

    public function testExcelFormatReturnsRaw()
    {
        $binaryContent = 'FAKE_XLS_CONTENT';
        $this->mock->setNextResponse(200, $binaryContent);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('format' => 'excel'));
        $result = $op->receive();

        $this->assertEquals($binaryContent, $result);
    }

    public function testInvalidPhoneConfigThrows()
    {
        $op = new ReceiveMessages($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->setConfigs(array('phone_number' => '123'));
    }

    public function testHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new ReceiveMessages($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->receive();
    }

    public function testMarkAsReadConfig()
    {
        $this->mock->setNextResponse(200, '');
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('mark_as_read' => true));
        $result = $op->receive();
        $this->assertIsArray($result);
    }

    public function testOnlyUnreadConfig()
    {
        $this->mock->setNextResponse(200, '');
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('only_unread' => true));
        $result = $op->receive();
        $this->assertIsArray($result);
    }

    public function testValidPhoneConfig()
    {
        $this->mock->setNextResponse(200, '');
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('phone_number' => '1234567890'));
        $result = $op->receive();
        $this->assertIsArray($result);
    }

    public function testDateParsingWithoutRawConfig()
    {
        $response = "1234567890\tHello\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $result = $op->receive();

        $this->assertCount(1, $result);
        $this->assertInstanceOf('\DateTime', $result[0]['date']);
    }

    public function testDefaultIncludeInternalIdIsTrue()
    {
        $response = "1234567890\tHello\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertArrayHasKey('internal_id', $result[0]);
    }

    public function testReceiveWith14DigitPhone()
    {
        $response = "54912345678901\tHello\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
        $this->assertEquals('54912345678901', $result[0]['phone_number']);
    }

    public function testSkipsMalformedLines()
    {
        $response = "malformed line without tabs\n1234567890\tHello\t2026-01-15 10:30:00\t999\tAb1";
        $this->mock->setNextResponse(200, $response);
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->receive();

        $this->assertCount(1, $result);
    }

    public function testAllConfigsTogether()
    {
        $this->mock->setNextResponse(200, '');
        $op = new ReceiveMessages($this->mock);
        $op->setConfigs(array(
            'phone_number' => '1234567890',
            'only_unread' => true,
            'mark_as_read' => true,
            'api_response_date' => 'raw',
        ));
        $result = $op->receive();
        $this->assertIsArray($result);
    }
}
