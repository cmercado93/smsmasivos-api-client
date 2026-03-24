<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\SendMessage;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class SendMessageTest extends TestCase
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

    public function testSendMessageSuccess()
    {
        $this->mock->setNextResponse(200, '0;Mensaje enviado');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello world');
        $this->assertTrue($result);
    }

    public function testSendMessageTestModeSuccess()
    {
        $this->mock->setNextResponse(200, '1;Simulacro OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello', array('test' => true));
        $this->assertTrue($result);
    }

    public function testSendMessageApiError()
    {
        $this->mock->setNextResponse(200, '-1;Telefono fijo');
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->sendMessage('1234567890', 'Hello');
    }

    public function testSendMessageHttpError()
    {
        $this->mock->setNextResponse(500, 'Internal Server Error');
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->sendMessage('1234567890', 'Hello');
    }

    public function testValidationTooLongMessage()
    {
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('1234567890', str_repeat('A', 161));
    }

    public function testValidationInvalidCharsInMessage()
    {
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('1234567890', "Hola con \xC3\xB1");
    }

    public function testValidationEmptyMessage()
    {
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('1234567890', '');
    }

    public function testValidationInvalidPhoneNumber()
    {
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('123', 'Hello');
    }

    public function testValidationInvalidPhoneChars()
    {
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('12345-67890', 'Hello');
    }

    public function testValidationReportsMultipleErrors()
    {
        $op = new SendMessage($this->mock);

        try {
            $op->sendMessage('123', str_repeat('A', 161));
            $this->fail('Expected ValidationException');
        } catch (\Cmercado93\SmsmasivosApi\Exceptions\ValidationException $e) {
            $data = $e->getExtraData();
            $this->assertArrayHasKey('message', $data);
            $this->assertArrayHasKey('phone_number', $data);
        }
    }

    public function testValidationSendDateMustBeDateTime()
    {
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('1234567890', 'Hello', array('send_date' => 'not a date'));
    }

    public function testValidationSendDateAcceptsDateTime()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);

        $result = $op->sendMessage('1234567890', 'Hello', array('send_date' => new \DateTime()));
        $this->assertTrue($result);
    }

    public function testNoCredentialsThrows()
    {
        Credentials::reset();

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\CredentialsException');
        new SendMessage($this->mock);
    }

    public function testApiResponseWithoutSemicolon()
    {
        $this->mock->setNextResponse(200, 'INVALID_RESPONSE');
        $op = new SendMessage($this->mock);

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->sendMessage('1234567890', 'Hello');
    }

    public function testSendWith8DigitPhone()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('12345678', 'Hello');
        $this->assertTrue($result);
    }

    public function testSendWithCountryCodePhone()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('5491234567890', 'Hello');
        $this->assertTrue($result);
    }

    public function testHtmlConfig()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello', array('html' => 1));
        $this->assertTrue($result);
    }

    public function testInternalIdConfig()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello', array('internal_id' => 'Ab123'));
        $this->assertTrue($result);
    }

    public function testAllConfigsTogether()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello', array(
            'internal_id' => 'Ab123',
            'test' => true,
            'send_date' => new \DateTime('2026-12-31 10:00:00'),
            'html' => 1,
        ));
        $this->assertTrue($result);
    }

    public function testApiResponseCodeOnly()
    {
        $this->mock->setNextResponse(200, '-1');
        $op = new SendMessage($this->mock);

        try {
            $op->sendMessage('1234567890', 'Hello');
            $this->fail('Expected ApiResponseException');
        } catch (\Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException $e) {
            $data = $e->getExtraData();
            $this->assertEquals(-1, $data['api_response'][0]['code']);
        }
    }

    public function testApiResponseMultipleSemicolons()
    {
        $this->mock->setNextResponse(200, '-5;Posible SPAM;detalle extra');
        $op = new SendMessage($this->mock);

        try {
            $op->sendMessage('1234567890', 'Hello');
            $this->fail('Expected ApiResponseException');
        } catch (\Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException $e) {
            $data = $e->getExtraData();
            $this->assertStringContainsString('Posible SPAM', $data['api_response'][0]['message']);
        }
    }

    public function testApiResponseWithUserPasswordAuth()
    {
        Credentials::setUserAndPassword('DEMO500', 'DEMO500');
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello');
        $this->assertTrue($result);
    }

    public function testInternalIdBoundary50Chars()
    {
        $this->mock->setNextResponse(200, '0;OK');
        $op = new SendMessage($this->mock);
        $result = $op->sendMessage('1234567890', 'Hello', array(
            'internal_id' => str_repeat('A', 50),
        ));
        $this->assertTrue($result);
    }

    public function testInternalId51CharsThrows()
    {
        $op = new SendMessage($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->sendMessage('1234567890', 'Hello', array(
            'internal_id' => str_repeat('A', 51),
        ));
    }
}
