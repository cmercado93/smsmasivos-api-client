<?php

namespace Cmercado93\SmsmasivosApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Cmercado93\SmsmasivosApi\Common\MessageValidation;

class MessageValidationTest extends TestCase
{
    protected $validator;

    protected function setUp(): void
    {
        $this->validator = new MessageValidation();
    }

    public function testValidMessageCharacters()
    {
        $this->assertTrue($this->validator->validateMessageCharacters('Hello World 123!'));
    }

    public function testInvalidMessageCharacters()
    {
        $this->assertFalse($this->validator->validateMessageCharacters("Hola mundo con \xC3\xB1"));
    }

    public function testEmptyMessageCharactersReturnsFalse()
    {
        $this->assertFalse($this->validator->validateMessageCharacters(''));
    }

    public function testNullMessageCharactersReturnsFalse()
    {
        $this->assertFalse($this->validator->validateMessageCharacters(null));
    }

    public function testCommaIsValidCharacter()
    {
        $this->assertTrue($this->validator->validateMessageCharacters('Hello, world'));
    }

    public function testAllSpecialCharsValid()
    {
        $this->assertTrue($this->validator->validateMessageCharacters('!?#$%()*+-./:;=@ ,'));
    }

    public function testValidMessageLength()
    {
        $this->assertTrue($this->validator->validateMessageLength(str_repeat('A', 160)));
    }

    public function testTooLongMessage()
    {
        $this->assertFalse($this->validator->validateMessageLength(str_repeat('A', 161)));
    }

    public function testValidPhoneNumberLength10Digits()
    {
        $this->assertTrue($this->validator->validatePhoneNumberLength('1234567890'));
    }

    public function testValidPhoneNumberLength8Digits()
    {
        $this->assertTrue($this->validator->validatePhoneNumberLength('12345678'));
    }

    public function testValidPhoneNumberLengthWithCountryCode()
    {
        $this->assertTrue($this->validator->validatePhoneNumberLength('541234567890'));
    }

    public function testInvalidPhoneNumberTooShort()
    {
        $this->assertFalse($this->validator->validatePhoneNumberLength('1234567'));
    }

    public function testValidPhoneNumberCharacters()
    {
        $this->assertTrue($this->validator->validatePhoneNumberCharacters('1234567890'));
    }

    public function testInvalidPhoneNumberCharacters()
    {
        $this->assertFalse($this->validator->validatePhoneNumberCharacters('123-456-7890'));
    }

    public function testValidInternalIdCharacters()
    {
        $this->assertTrue($this->validator->validateInternalIdCharacters('Ab123'));
    }

    public function testInvalidInternalIdCharacters()
    {
        $this->assertFalse($this->validator->validateInternalIdCharacters('Ab-123'));
    }

    public function testValidInternalIdLength()
    {
        $this->assertTrue($this->validator->validateInternalIdLength(str_repeat('A', 50)));
    }

    public function testTooLongInternalId()
    {
        $this->assertFalse($this->validator->validateInternalIdLength(str_repeat('A', 51)));
    }
}
