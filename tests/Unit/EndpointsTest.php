<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Common\Endpoints;

class EndpointsTest extends TestCase
{
    public function testUrlGeneralIsDefined()
    {
        $this->assertNotEmpty(Endpoints::URL_GENERAL);
        $this->assertStringStartsWith('https://', Endpoints::URL_GENERAL);
    }

    public function testAllEndpointsAreDefined()
    {
        $this->assertNotEmpty(Endpoints::URI_SEND_MESSAGE);
        $this->assertNotEmpty(Endpoints::URI_SEND_MESSAGE_BLOCK);
        $this->assertNotEmpty(Endpoints::CHECK_SENT_BLOCK);
        $this->assertNotEmpty(Endpoints::GET_MESSAGES_INBOX);
        $this->assertNotEmpty(Endpoints::GET_BALANCE);
        $this->assertNotEmpty(Endpoints::GET_PACKAGE_EXPIRATION);
        $this->assertNotEmpty(Endpoints::GET_NUMBER_MESSAGES_SENT);
        $this->assertNotEmpty(Endpoints::GET_CURRENT_DATE_SERVER);
    }

    public function testGetCurrentDateServerHasNoQueryParams()
    {
        $this->assertStringNotContainsString('?', Endpoints::GET_CURRENT_DATE_SERVER);
    }
}
