<?php

namespace Flow\Tests\Feature;

use Random\RandomException;

class EncryptedDataSeeder
{
    /**
     * @param positive-int $length
     * @return string
     * @throws RandomException
     */
    public static function randomData(int $length = 16): string
    {
        return base64_encode(random_bytes($length));
    }

    public function encryptData(): void {}
}
