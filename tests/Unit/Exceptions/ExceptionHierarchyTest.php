<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException;
use Cmercado93\SmsmasivosApi\Exceptions\ValidationException;
use Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException;
use Cmercado93\SmsmasivosApi\Exceptions\CredentialsException;

class ExceptionHierarchyTest extends TestCase
{
    public function testSmsmasivosExceptionExtendsException()
    {
        $e = new SmsmasivosException('test', 1, array('key' => 'val'));
        $this->assertInstanceOf('\Exception', $e);
        $this->assertEquals('test', $e->getMessage());
        $this->assertEquals(1, $e->getCode());
        $this->assertEquals(array('key' => 'val'), $e->getExtraData());
    }

    public function testValidationExceptionExtendsSmsmasivos()
    {
        $data = array('message' => array(array('message' => 'too long', 'code' => -6)));
        $e = new ValidationException($data);
        $this->assertInstanceOf('Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException', $e);
        $this->assertEquals(101, $e->getCode());
        $this->assertStringContainsString('1 error(s)', $e->getMessage());
        $this->assertEquals($data, $e->getExtraData());
    }

    public function testApiResponseExceptionExtendsSmsmasivos()
    {
        $data = array('api_response' => array(array('message' => 'Server error', 'code' => -99)));
        $e = new ApiResponseException($data);
        $this->assertInstanceOf('Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException', $e);
        $this->assertEquals(102, $e->getCode());
        $this->assertEquals('Server error', $e->getMessage());
    }

    public function testApiResponseExceptionDefaultMessage()
    {
        $e = new ApiResponseException(array());
        $this->assertEquals('API error', $e->getMessage());
    }

    public function testCredentialsExceptionExtendsSmsmasivos()
    {
        $e = new CredentialsException();
        $this->assertInstanceOf('Cmercado93\SmsmasivosApi\Exceptions\SmsmasivosException', $e);
        $this->assertEquals(100, $e->getCode());
        $this->assertStringContainsString('No authentication configured', $e->getMessage());
    }
}
