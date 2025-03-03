<?php

namespace Flow\Tests\Unit;

use Flow\Core\Validation;
use Flow\Tests\Unit\Methods\RSA;
use PHPUnit\Framework\TestCase;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;

/**
 * @covers \Flow\Core\Validation
 */
class ValidationTest extends TestCase
{
    public function testRSAValidation(): void
    {
        $public = RSA::createPublicKey(2048);
        $this->assertTrue(Validation::RSAPublicKey($public));
    }

    public function testRSAValidationBadStartString(): void
    {
        $this->expectException(ValidationException::class);
        $public = RSA::createPublicKey(2048);
        $this->assertTrue(Validation::RSAPublicKey('test' . $public));
    }

    public function testValidatorEncryptedData(): void
    {
        $this->assertTrue(Validation::encryptedData('38HTxVC39q7bpiuRcfVonPZR1eIHDMD0XL302zd0+pBm56TJIXEbcTtdcASbIYSe'));
        $this->assertTrue(Validation::encryptedData('DyNvFhjpnPcKOXk3j4tXjg=='));
        $this->assertTrue(Validation::encryptedData('8DD7/VuH3z3yQ+PFE6a8r+3dkp4o0HeLqFcFrzT/oJY='));
    }

    public function testBadValidation(): void
    {
        $this->expectException(ValidationException::class);
        $string = 'aaaaaaaaaaaaaaaa';
        Validation::encryptedData($string);
    }

    public function testEncryptedDataBadValidationByJsonDecoding(): void
    {
        $this->expectException(ValidationException::class);
        $string = '1';
        Validation::encryptedData($string);
    }

    public function testEncryptedDataIsRandom(): void
    {
        $this->expectException(ValidationException::class);
        $string = 'TjqlWqg8q8JDLLHbWjL3sViIEw+DQXDrMKbsUGm5R3E=';
        Validation::encryptedData($string);
    }

    public function testBadValidation2(): void
    {
        $this->expectException(ValidationException::class);
        $string = 'MTExMTExMTExMTExMTExMQ==';
        Validation::encryptedData($string);
    }

    public function testBadValidation3(): void
    {
        $this->expectException(ValidationException::class);
        $string = 'MTIzMTIzMTI=';
        Validation::encryptedData($string);
    }

    public function testBadValidation_string16(): void
    {
        $this->expectException(ValidationException::class);
        $string = 'cXdlcmFzZGZ6eGN2dHl1aQ==';
        Validation::encryptedData($string);
    }
}
