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

    public function encrypt(string $message, string $key): string
    {
        $encryptor = new Encrypt();
        return $encryptor->encrypt($message, $key, $this->state);
    }

    public function decrypt(string $ciphertext, string $key): string
    {
        $decryptor = new Decrypt();
        return $decryptor->decrypt($ciphertext, $key, $this->state);
    }
}


?>