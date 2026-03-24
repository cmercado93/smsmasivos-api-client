<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\CheckMessageBlockSent;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class CheckMessageBlockSentTest extends TestCase
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

    public function testPendingReturnsFalse()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $result = $op->check();
        $this->assertFalse($result);
    }

    public function testParseSentOk()
    {
        $response = "Ab123\t1711234567\tOK";
        $this->mock->setNextResponse(200, $response);
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->check();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Ab123', $result[0]['internal_id']);
        $this->assertTrue($result[0]['sent']);
    }

    public function testParseSentError()
    {
        $response = "Ab123\t1711234567\tError: telefono fijo";
        $this->mock->setNextResponse(200, $response);
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->check();

        $this->assertCount(1, $result);
        $this->assertFalse($result[0]['sent']);
        $this->assertNotEmpty($result[0]['error']);
    }

    public function testParseMultipleResults()
    {
        $response = "Ab123\t1711234567\tOK\nAb124\t1711234568\tError";
        $this->mock->setNextResponse(200, $response);
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->check();

        $this->assertCount(2, $result);
    }

    public function testFilterByDate()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('date', new \DateTime('2026-01-01'));
        $result = $op->check();
        $this->assertFalse($result);
    }

    public function testInvalidFilterThrows()
    {
        $op = new CheckMessageBlockSent($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->setFilter('invalid_filter', 'value');
    }

    public function testDateFilterRequiresDateTime()
    {
        $op = new CheckMessageBlockSent($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ValidationException');
        $op->setFilter('date', 'not a date');
    }

    public function testHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->check();
    }

    public function testUsesGetNotPost()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->check();
        $this->assertTrue(true);
    }

    public function testMarkAsReadConfig()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('mark_as_read' => true));
        $result = $op->check();
        $this->assertFalse($result);
    }

    public function testOnlyUnreadConfig()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('only_unread' => true));
        $result = $op->check();
        $this->assertFalse($result);
    }

    public function testMarkAsReadAndOnlyUnreadTogether()
    {
        $this->mock->setNextResponse(200, 'PENDIENTE');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('mark_as_read' => true, 'only_unread' => true));
        $result = $op->check();
        $this->assertFalse($result);
    }

    public function testDateParsingWithoutRawConfig()
    {
        $response = "Ab123\t1711234567\tOK";
        $this->mock->setNextResponse(200, $response);
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $result = $op->check();

        $this->assertCount(1, $result);
        $this->assertInstanceOf('\DateTime', $result[0]['date']);
    }

    public function testPendingCaseInsensitive()
    {
        $this->mock->setNextResponse(200, '  pendiente  ');
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $result = $op->check();
        $this->assertFalse($result);
    }

    public function testOkCaseInsensitiveInResult()
    {
        $response = "Ab123\t1711234567\tok";
        $this->mock->setNextResponse(200, $response);
        $op = new CheckMessageBlockSent($this->mock);
        $op->setFilter('internal_id', 'Ab123');
        $op->setConfigs(array('api_response_date' => 'raw'));
        $result = $op->check();

        $this->assertTrue($result[0]['sent']);
    }

    public function testValidationReportsAllFilterErrors()
    {
        $op = new CheckMessageBlockSent($this->mock);
        try {
            $op->setFilter('internal_id', str_repeat('A', 51));
            $this->fail('Expected ValidationException');
        } catch (\Cmercado93\SmsmasivosApi\Exceptions\ValidationException $e) {
            $data = $e->getExtraData();
            $this->assertArrayHasKey('filter_value', $data);
        }
    }
}
