<?php

namespace Flow\Tests\Helpers;

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
        return random_bytes($length);
    }
}
