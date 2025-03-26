<?php

namespace Flow\Core;

use Flow\Core\Enums\ValidatorEnum;
use Flow\Core\Validations\EncryptedData;
use Flow\Core\Validations\RsaPublicKey;
use Flow\Core\Validations\ValidationInterface;
use VladViolentiy\VivaFramework\Exceptions\ValidationException;

class Validation
{
    /** @var array<string, ValidationInterface>  */
    private static array $objects = [];

    /**
     * @param string $keyInput
     * @return bool
     * @phpstan-assert non-empty-string $keyInput
     * @throws ValidationException
     */
    public static function RSAPublicKey(string $keyInput): bool
    {
        $value = ValidatorEnum::RSAKey->value;
        if (!isset(self::$objects[$value])) {
            self::$objects[$value] = new RsaPublicKey();
        }

        return self::$objects[$value]->validate($keyInput);
    }

    public static function encryptedData(string $data): bool
    {
        $value = ValidatorEnum::EncryptedData->value;
        if (!isset(self::$objects[$value])) {
            self::$objects[$value] = new EncryptedData();
        }

        return self::$objects[$value]->validate($data);
    }
}
