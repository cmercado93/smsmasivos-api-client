<?php

namespace Cmercado93\SmsmasivosApi\Tests\Operations;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Operations\GetPackageExpiration;
use Cmercado93\SmsmasivosApi\Tests\Mock\MockHttpRequest;

class GetPackageExpirationTest extends TestCase
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

    public function testReturnsDateTimeForValidDate()
    {
        $this->mock->setNextResponse(200, '2026-12-31');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertInstanceOf('\DateTime', $result);
        $this->assertEquals('2026-12-31', $result->format('Y-m-d'));
    }

    public function testAcceptsMonthMarch()
    {
        $this->mock->setNextResponse(200, '2026-03-15');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertInstanceOf('\DateTime', $result);
    }

    public function testAcceptsMonthSeptember()
    {
        $this->mock->setNextResponse(200, '2026-09-30');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertInstanceOf('\DateTime', $result);
    }

    public function testAcceptsAllMonths()
    {
        for ($m = 1; $m <= 12; $m++) {
            $date = sprintf('2026-%02d-15', $m);
            $this->mock->setNextResponse(200, $date);
            $op = new GetPackageExpiration($this->mock);
            $result = $op->get();
            $this->assertInstanceOf('\DateTime', $result, "Failed for month {$m}");
        }
    }

    public function testReturnsFalseForInvalidDate()
    {
        $this->mock->setNextResponse(200, 'not-a-date');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testHttpErrorThrows()
    {
        $this->mock->setNextResponse(500, 'Error');
        $op = new GetPackageExpiration($this->mock);
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\ApiResponseException');
        $op->get();
    }

    public function testRejectsYear1999()
    {
        $this->mock->setNextResponse(200, '1999-12-31');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testRejectsMonth00()
    {
        $this->mock->setNextResponse(200, '2026-00-15');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testRejectsMonth13()
    {
        $this->mock->setNextResponse(200, '2026-13-15');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testRejectsDay00()
    {
        $this->mock->setNextResponse(200, '2026-01-00');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testRejectsDay32()
    {
        $this->mock->setNextResponse(200, '2026-01-32');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertFalse($result);
    }

    public function testTrimsWhitespace()
    {
        $this->mock->setNextResponse(200, '  2026-06-15  ');
        $op = new GetPackageExpiration($this->mock);
        $result = $op->get();
        $this->assertInstanceOf('\DateTime', $result);
    }
}
