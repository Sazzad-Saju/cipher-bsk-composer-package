<?php

namespace CipherBsk\Tests;

use CipherBsk\CipherBsk;
use PHPUnit\Framework\TestCase;

class SecureHashTest extends TestCase
{
    private CipherBsk $cipherBsk;

    protected function setUp(): void
    {
        $this->cipherBsk = new CipherBsk();
        // Set a default key for tests to prevent exceptions
        putenv('CIPHER_BSK_KEY=test-key-for-phpunit');
    }

    protected function tearDown(): void
    {
        // Clean up environment variables after each test
        putenv('CIPHER_BSK_KEY');
        putenv('APP_KEY');
    }

    /**
     * Test that secureHash produces a valid bcrypt hash
     */
    public function testSecureHashProducesValidBcryptHash(): void
    {
        $password = 'Hstu@5200';
        $hash = $this->cipherBsk->secureHash($password);

        // Bcrypt hashes are 60 characters long and start with $2y$
        $this->assertMatchesRegularExpression('/^\$2y\$/', $hash);
        $this->assertEquals(60, strlen($hash));
    }

    /**
     * Test that secureHash produces different hashes for the same password
     * (due to bcrypt salt)
     */
    public function testSecureHashProducesDifferentHashesForSamePassword(): void
    {
        $password = 'sazzadhossainsaju@5200';
        
        $hash1 = $this->cipherBsk->secureHash($password);
        $hash2 = $this->cipherBsk->secureHash($password);

        $this->assertNotEquals($hash1, $hash2);
        $this->assertMatchesRegularExpression('/^\$2y\$/', $hash1);
        $this->assertMatchesRegularExpression('/^\$2y\$/', $hash2);
    }

    /**
     * Test verifyHash with correct password
     */
    public function testVerifyHashWithCorrectPassword(): void
    {
        $password = 'Hello, World!';
        $hash = $this->cipherBsk->secureHash($password);

        $this->assertTrue($this->cipherBsk->verifyHash($password, $hash));
    }

    /**
     * Test verifyHash with incorrect password
     */
    public function testVerifyHashWithIncorrectPassword(): void
    {
        $password = 'Hstu@5200';
        $wrongPassword = 'wrong@5200';
        $hash = $this->cipherBsk->secureHash($password);

        $this->assertFalse($this->cipherBsk->verifyHash($wrongPassword, $hash));
    }

    /**
     * Test complete secureHash and verifyHash cycle
     */
    public function testSecureHashVerifyHashCycle(): void
    {
        $passwords = [
            'admin',
            'Hajee Mohammad Danesh Science and Technology University@52#$',
            'sazzadhossainsaju@5200',
            'Hello, World!',
            'cipher-bsk-package',
            ''  // empty password
        ];

        foreach ($passwords as $password) {
            $hash = $this->cipherBsk->secureHash($password);
            
            // Verify correct password
            $this->assertTrue(
                $this->cipherBsk->verifyHash($password, $hash),
                "Failed to verify password: '$password'"
            );
            
            // Verify incorrect password (unless empty)
            if ($password !== '') {
                $this->assertFalse(
                    $this->cipherBsk->verifyHash($password . 'wrong', $hash),
                    "Incorrectly verified wrong password for: '$password'"
                );
            }
        }
    }

    /**
     * Test secureHash with custom environment key
     */
    public function testSecureHashWithCustomEnvironmentKey(): void
    {
        putenv('CIPHER_BSK_KEY=Hstu@5200');
        
        $password = 'Hello, World!';
        $hash1 = $this->cipherBsk->secureHash($password);
        
        putenv('CIPHER_BSK_KEY=sazzadhossainsaju@5200');
        $cipherBsk2 = new CipherBsk(); // New instance to get new key
        $hash2 = $cipherBsk2->secureHash($password);

        // Hashes should be different because underlying encryption uses different keys
        $this->assertNotEquals($hash1, $hash2);
        
        // But verification should fail when key is different
        $this->assertFalse($cipherBsk2->verifyHash($password, $hash1));
    }

    /**
     * Test secureHash with APP_KEY fallback
     */
    public function testSecureHashWithAppKeyFallback(): void
    {
        putenv('APP_KEY=cipher-bsk-app-key');
        
        $password = 'admin';
        $hash = $this->cipherBsk->secureHash($password);
        
        $this->assertTrue($this->cipherBsk->verifyHash($password, $hash));
        $this->assertMatchesRegularExpression('/^\$2y\$/', $hash);
    }

    /**
     * Test that different keys produce different underlying encryption
     */
    public function testDifferentKeysProduceDifferentHashes(): void
    {
        $password = 'Hello, World!';
        
        // Set first key
        putenv('CIPHER_BSK_KEY=Hstu@5200');
        $cipherBsk1 = new CipherBsk();
        
        // Set different key  
        putenv('CIPHER_BSK_KEY=sazzadhossainsaju@5200');
        $cipherBsk2 = new CipherBsk();
        
        // Test that the underlying encryption is different
        $encrypted1 = $cipherBsk1->encrypt($password, 'Hstu@5200');
        $encrypted2 = $cipherBsk2->encrypt($password, 'sazzadhossainsaju@5200');
        $this->assertNotEquals($encrypted1, $encrypted2, 'Different keys should produce different encrypted values');
        
        // Now test the secure hashes
        $hash1 = $cipherBsk1->secureHash($password);
        $hash2 = $cipherBsk2->secureHash($password);

        // The final bcrypt hashes should be different because the underlying
        // CipherBSK encryption produces different results with different keys
        $this->assertNotEquals($hash1, $hash2, 'Different keys should produce different hashes');
        
        // Each instance should be able to verify its own hash
        $this->assertTrue($cipherBsk1->verifyHash($password, $hash1));
        $this->assertTrue($cipherBsk2->verifyHash($password, $hash2));
    }

    /**
     * Test edge cases and error conditions
     */
    public function testEdgeCases(): void
    {
        // Very long password
        $longPassword = str_repeat('Hajee Mohammad Danesh Science and Technology University ', 20);
        $hash = $this->cipherBsk->secureHash($longPassword);
        $this->assertTrue($this->cipherBsk->verifyHash($longPassword, $hash));

        // Password with null bytes
        $nullBytePassword = "cipher\0bsk\0package";
        $hash = $this->cipherBsk->secureHash($nullBytePassword);
        $this->assertTrue($this->cipherBsk->verifyHash($nullBytePassword, $hash));

        // Single character
        $singleChar = '@';
        $hash = $this->cipherBsk->secureHash($singleChar);
        $this->assertTrue($this->cipherBsk->verifyHash($singleChar, $hash));
    }

    /**
     * Test that verifyHash fails with invalid hash format
     */
    public function testVerifyHashWithInvalidHashFormat(): void
    {
        $password = 'Hstu@5200';
        
        // Invalid hash formats
        $invalidHashes = [
            'cipher-bsk',
            '$2y$10$invalid',
            '',
            'sazzadhossainsaju',
            '$1$invalid$hash',  // MD5 format
        ];

        foreach ($invalidHashes as $invalidHash) {
            $this->assertFalse(
                $this->cipherBsk->verifyHash($password, $invalidHash),
                "Should return false for invalid hash: '$invalidHash'"
            );
        }
    }

    /**
     * Test performance with multiple operations
     */
    public function testPerformanceWithMultipleOperations(): void
    {
        $password = 'cipher-bsk-package';
        $iterations = 10;
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $hash = $this->cipherBsk->secureHash($password);
            $this->assertTrue($this->cipherBsk->verifyHash($password, $hash));
        }
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        // Should complete 10 operations in reasonable time (adjust as needed)
        $this->assertLessThan(10.0, $totalTime, 'Performance test took too long');
    }

    /**
     * Test that missing encryption key throws exception
     */
    public function testMissingEncryptionKeyThrowsException(): void
    {
        // Clear all environment variables
        putenv('CIPHER_BSK_KEY');
        putenv('APP_KEY');
        
        $cipherBsk = new CipherBsk();
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No encryption key configured');
        
        $cipherBsk->secureHash('test-password');
    }
}