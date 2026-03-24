<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\SendMessagesInBlock;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class SendMessagesInBlockTest extends TestCase
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

    public function testSendBlockSuccess()
    {
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello 1'),
            array('phone_number' => '0987654321', 'message' => 'Hello 2'),
        ));

        $this->assertTrue($op->send());
    }

    public function testSendBlockApiError()
    {
        $this->mock->setNextResponse(200, 'ERROR: credenciales invalidas');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->send();
    }

    public function testValidationInvalidPhoneInBlock()
    {
        $op = new SendMessagesInBlock($this->mock);
        $op->setConfigs(array());

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->setMessageBlock(array(
            array('phone_number' => '123', 'message' => 'Hello'),
        ));
    }

    public function testValidationReportsAllErrorsInBlock()
    {
        $op = new SendMessagesInBlock($this->mock);
        $op->setConfigs(array());

        try {
            $op->setMessageBlock(array(
                array('phone_number' => '123', 'message' => str_repeat('A', 161)),
            ));
            $this->fail('Expected ValidationException');
        } catch (\Cmercado93\SmsmasivosApi\Exceptions\ValidationException $e) {
            $data = $e->getExtraData();
            $this->assertNotEmpty($data['messages']);
            $msg = $data['messages'][0];
            $this->assertArrayHasKey('message', $msg);
            $this->assertArrayHasKey('phone_number', $msg);
        }
    }

    public function testTestConfigUsesTestKey()
    {
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array('test' => true));
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->assertTrue($op->send());
    }

    public function testHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->send();
    }

    public function testTabFieldSeparator()
    {
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array('field_separator' => 'tab'));
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->assertTrue($op->send());
    }

    public function testDefaultFieldSeparatorIsComma()
    {
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->assertTrue($op->send());
    }

    public function testBlockWithInternalId()
    {
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello', 'internal_id' => 'Id1'),
        ));

        $this->assertTrue($op->send());
    }

    public function testBlockWithoutInternalIdUsesPhoneAsDefault()
    {
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->assertTrue($op->send());
    }

    public function testApiResponseOkCaseInsensitive()
    {
        $this->mock->setNextResponse(200, '  ok  ');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->assertTrue($op->send());
    }

    public function testCrlfSeparator()
    {
        $this->assertEquals("\r\n", SendMessagesInBlock::FIELD_SEPARATOR_ENTER);
    }

    public function testUserPasswordAuth()
    {
        Credentials::setUserAndPassword('DEMO500', 'DEMO500');
        $this->mock->setNextResponse(200, 'OK');
        $op = new SendMessagesInBlock($this->mock);

        $op->setConfigs(array());
        $op->setMessageBlock(array(
            array('phone_number' => '1234567890', 'message' => 'Hello'),
        ));

        $this->assertTrue($op->send());
    }
}
