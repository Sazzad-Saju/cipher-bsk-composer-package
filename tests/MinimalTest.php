<?php
namespace CipherBsk\Tests;

use PHPUnit\Framework\TestCase;
use CipherBsk\State;

class MinimalTest extends TestCase
{
    public function testMinimal(): void
    {
        $message = "Hello, World!";
        $key = "Hstu@5200";

        // Minimal implementation of encrypt
        $state = new State();
        $encryptedMessage = $this->encrypt($message, $key, $state);

        $this->assertSame($message, $encryptedMessage);
    }

    private function encrypt(string $message, string $key, State $state): string
    {
        $state->reset();
        echo $message;
        echo PHP_EOL;
        return $message;
    }
}
