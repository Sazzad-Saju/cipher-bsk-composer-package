<?php

/**
 * CipherBsk Configuration
 * 
 * Note: This config file will only be used when the package is installed
 * in a Laravel application. The env() helper function will be available
 * in that context.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Cipher-BSK Configuration
    |--------------------------------------------------------------------------
    |
    | Define a custom key if desired. By default, it will use APP_KEY from the
    | Laravel environment. This ensures consistent encryption/decryption.
    |
    */

    'key' => env('CIPHER_BSK_KEY', env('APP_KEY')),
];
