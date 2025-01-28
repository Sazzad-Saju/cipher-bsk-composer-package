<?php

namespace CipherBsk\Tests;

use CipherBsk\CipherBsk;
use PHPUnit\Framework\TestCase;

class CipherBskTest extends TestCase
{
    private CipherBsk $cipherBsk;

    protected function setUp(): void
    {
        $this->cipherBsk = new CipherBsk();
    }

    public function testEncryptWithEmptyValues(): void
    {
        $this->assertSame('', $this->cipherBsk->encrypt('', ''));
        $this->assertSame('', $this->cipherBsk->encrypt('message', ''));
    }

    public function testDecryptWithEmptyValues(): void
    {
        $this->assertSame('', $this->cipherBsk->decrypt('', ''));
        $this->assertSame('', $this->cipherBsk->decrypt('ciphertext', ''));
    }

    public function testEncryptDecryptCycle(): void
    {
        $message = "Hello, World!";
        $key = "Hstu@5200";

        $encryptedMessage = $this->cipherBsk->encrypt($message, $key);
        $this->assertNotSame($message, $encryptedMessage);
        $decryptedMessage = $this->cipherBsk->decrypt($encryptedMessage, $key);
        $this->assertSame($message, $decryptedMessage);
    }

    public function testPaddingInEncryption(): void
    {
        $message = "Hajee Mohammad Danesh Science and Technology University";
        $key = "sazzadhossainsaju@5200";

        $encryptedMessage = $this->cipherBsk->encrypt($message, $key);
        $this->assertNotSame($message, $encryptedMessage);

        $decryptedMessage = $this->cipherBsk->decrypt($encryptedMessage, $key);
        $this->assertSame($message, $decryptedMessage);
    }
}
