<?php

namespace CipherBsk;

use CipherBsk\Cipher\Encrypt;
use CipherBsk\Cipher\Decrypt;
use CipherBsk\State;

class CipherBsk
{
    private $state;

    public function __construct()
    {
        $this->state = new State();
    }

    /**
     * Basic encryption using CipherBSK algorithm
     */
    public function encrypt(string $message, string $key): string
    {
        $encryptor = new Encrypt();
        return $encryptor->encrypt($message, $key, $this->state);
    }

    /**
     * Basic decryption using CipherBSK algorithm
     */
    public function decrypt(string $ciphertext, string $key): string
    {
        $decryptor = new Decrypt();
        return $decryptor->decrypt($ciphertext, $key, $this->state);
    }

    /**
     * Secure hash: Encrypt + bcrypt
     */
    public function secureHash(string $password): string
    {
        $key = $this->resolveKey();

        $encrypted = $this->encrypt($password, $key);

        return password_hash($encrypted, PASSWORD_DEFAULT);
    }

    /**
     * Verify encrypted password with stored hash
     */
    public function verifyHash(string $plainPassword, string $hashedPassword): bool
    {
        $key = $this->resolveKey();

        $encrypted = $this->encrypt($plainPassword, $key);

        return password_verify($encrypted, $hashedPassword);
    }

    /**
     * Resolve the key from config or environment
     */
    private function resolveKey(): string
    {
        // Try Laravel config first (if available)
        if ($this->isLaravelEnvironment()) {
            try {
                if (function_exists('config')) {
                    $configKey = call_user_func('config', 'cipher-bsk.key');
                    if ($configKey) {
                        return $configKey;
                    }
                }
                
                if (function_exists('env')) {
                    $envKey = call_user_func('env', 'APP_KEY');
                    if ($envKey) {
                        return $envKey;
                    }
                }
            } catch (\Throwable $e) {
                // Laravel helpers failed, fall back to getenv
            }
        }

        // Standalone PHP environment
        $cipherKey = getenv('CIPHER_BSK_KEY');
        if ($cipherKey) {
            return $cipherKey;
        }
        
        $appKey = getenv('APP_KEY');
        if ($appKey) {
            return $appKey;
        }

        // No key found - throw exception to force proper configuration
        throw new \RuntimeException(
            'No encryption key configured. Please set one of the following environment variables: ' .
            'CIPHER_BSK_KEY or APP_KEY. ' .
            'For Laravel projects: run "php artisan vendor:publish --tag=config" to publish the config file.'
        );
    }

    /**
     * Check if we're running in a Laravel environment
     */
    private function isLaravelEnvironment(): bool
    {
        return function_exists('config') && function_exists('env');
    }
}