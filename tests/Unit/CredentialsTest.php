<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Credentials;
use Cmercado93\SmsmasivosApi\Exceptions\CredentialsException;

class CredentialsTest extends TestCase
{
    protected function tearDown(): void
    {
        Credentials::reset();
    }

    public function testSetUserAndPassword()
    {
        Credentials::setUserAndPassword('user1', 'pass1');

        $auth = Credentials::getAuthParams();

        $this->assertIsArray($auth);
        $this->assertEquals('user1', $auth['usuario']);
        $this->assertEquals('pass1', $auth['clave']);
    }

    public function testSetApiKey()
    {
        Credentials::setApiKey('MY_KEY');

        $auth = Credentials::getAuthParams();

        $this->assertIsArray($auth);
        $this->assertEquals('MY_KEY', $auth['apikey']);
    }

    public function testApiKeyClearsUserPassword()
    {
        Credentials::setUserAndPassword('user1', 'pass1');
        Credentials::setApiKey('MY_KEY');

        $auth = Credentials::getAuthParams();

        $this->assertArrayHasKey('apikey', $auth);
        $this->assertArrayNotHasKey('usuario', $auth);
    }

    public function testUserPasswordClearsApiKey()
    {
        Credentials::setApiKey('MY_KEY');
        Credentials::setUserAndPassword('user1', 'pass1');

        $auth = Credentials::getAuthParams();

        $this->assertArrayHasKey('usuario', $auth);
        $this->assertArrayNotHasKey('apikey', $auth);
    }

    public function testExistsCredentialsReturnsFalseWhenEmpty()
    {
        $this->assertFalse(Credentials::existsCredentials());
    }

    public function testExistsCredentialsReturnsTrueWithApiKey()
    {
        Credentials::setApiKey('KEY');
        $this->assertTrue(Credentials::existsCredentials());
    }

    public function testExistsCredentialsThrowsWhenEmptyAndThrowTrue()
    {
        $this->expectException('Cmercado93\SmsmasivosApi\Exceptions\CredentialsException');
        Credentials::existsCredentials(true);
    }

    public function testResetClearsAll()
    {
        Credentials::setApiKey('KEY');
        Credentials::reset();
        $this->assertFalse(Credentials::getAuthParams());
    }

    public function testTrimsWhitespace()
    {
        Credentials::setApiKey('  KEY  ');
        $auth = Credentials::getAuthParams();
        $this->assertEquals('KEY', $auth['apikey']);
    }

    public function testEmptyStringIsNotValid()
    {
        Credentials::setApiKey('');
        $this->assertFalse(Credentials::getAuthParams());
    }
}
