<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;

class CredentialsEdgeCasesTest extends TestCase
{
    protected function tearDown(): void
    {
        Credentials::reset();
    }

    public function testZeroStringApiKeyIsValid()
    {
        Credentials::setApiKey('0');
        $auth = Credentials::getAuthParams();
        $this->assertIsArray($auth);
        $this->assertEquals('0', $auth['apikey']);
    }

    public function testZeroStringUserAndPasswordIsValid()
    {
        Credentials::setUserAndPassword('0', '0');
        $auth = Credentials::getAuthParams();
        $this->assertIsArray($auth);
        $this->assertEquals('0', $auth['usuario']);
        $this->assertEquals('0', $auth['clave']);
    }

    public function testWhitespaceOnlyApiKeyIsInvalid()
    {
        Credentials::setApiKey('   ');
        $this->assertFalse(Credentials::getAuthParams());
    }

    public function testWhitespaceOnlyUserIsInvalid()
    {
        Credentials::setUserAndPassword('   ', 'pass');
        $this->assertFalse(Credentials::getAuthParams());
    }

    public function testWhitespaceOnlyPasswordIsInvalid()
    {
        Credentials::setUserAndPassword('user', '   ');
        $this->assertFalse(Credentials::getAuthParams());
    }

    public function testEmptyUserWithPasswordIsInvalid()
    {
        Credentials::setUserAndPassword('', 'pass');
        $this->assertFalse(Credentials::getAuthParams());
    }

    public function testUserWithEmptyPasswordIsInvalid()
    {
        Credentials::setUserAndPassword('user', '');
        $this->assertFalse(Credentials::getAuthParams());
    }
}
