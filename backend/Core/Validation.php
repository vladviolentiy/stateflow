<?php

namespace Flow\Core;

use Flow\Core\Enums\ValidatorEnum;
use Flow\Core\Validations\EncryptedDataValidator;
use Flow\Core\Validations\RsaPublicKeyValidator;
use Flow\Core\Validations\ValidationInterface;

class Validation
{
    /** @var array<string, ValidationInterface>  */
    private static array $objects = [];

    public static function rsaPublicKey(): ValidationInterface
    {
        $value = ValidatorEnum::RSAKey->value;
        if (!isset(self::$objects[$value])) {
            self::$objects[$value] = new RsaPublicKeyValidator();
        }

        return self::$objects[$value];
    }

    public static function encryptedData(string $data, string $field = ''): bool
    {
        $value = ValidatorEnum::EncryptedData->value;
        if (!isset(self::$objects[$value])) {
            self::$objects[$value] = new EncryptedDataValidator();
        }

        return self::$objects[$value]->validate($data, $field);
    }
}
