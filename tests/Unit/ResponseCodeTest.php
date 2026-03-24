<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Common\ResponseCode;

class ResponseCodeTest extends TestCase
{
    public function testOkIsZero()
    {
        $this->assertSame(0, ResponseCode::OK);
    }

    public function testTestOkIsOne()
    {
        $this->assertSame(1, ResponseCode::TEST_OK);
    }

    public function testNegativeCodesAreDefined()
    {
        $this->assertSame(-1, ResponseCode::LANDLINE);
        $this->assertSame(-2, ResponseCode::TOO_MANY_FAILURES);
        $this->assertSame(-3, ResponseCode::UNSUBSCRIBED);
        $this->assertSame(-4, ResponseCode::DUPLICATE_MESSAGE);
        $this->assertSame(-5, ResponseCode::SPAM);
        $this->assertSame(-6, ResponseCode::MESSAGE_TOO_LONG);
        $this->assertSame(-7, ResponseCode::INVALID_NUMBER_LENGTH);
        $this->assertSame(-8, ResponseCode::INVALID_NUMBER_CHARS);
        $this->assertSame(-9, ResponseCode::INVALID_AREA_CODE);
        $this->assertSame(-10, ResponseCode::CARRIER_REJECTED);
        $this->assertSame(-11, ResponseCode::INVALID_MESSAGE_CHARS);
        $this->assertSame(-12, ResponseCode::INVALID_NUMBER_PREFIX);
        $this->assertSame(-14, ResponseCode::DO_NOT_CALL_LIST);
        $this->assertSame(-99, ResponseCode::OTHER);
    }
}
