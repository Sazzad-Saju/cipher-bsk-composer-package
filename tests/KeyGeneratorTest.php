<?php

use PHPUnit\Framework\TestCase;
use CipherBsk\Cipher\KeyGenerator;
use CipherBsk\State;

class KeyGeneratorTest extends TestCase
{
    private $keyGenerator;
    private $state;

    protected function setUp(): void
    {
        $this->keyGenerator = new KeyGenerator();
        $this->state = new State();
    }

    public function testGenerateKeyPair_ShouldGenerateSubkeysForGivenKeyAndMessageLength()
    {
        $key = 'Lynkto@1200';
        $messageLength = 16;

        $result = $this->keyGenerator->generateKeyPair($messageLength, $key, $this->state);

        $this->assertArrayHasKey('subkey1', $result);
        $this->assertArrayHasKey('subkey2', $result);
        $this->assertNotEmpty($result['subkey1']);
        $this->assertNotEmpty($result['subkey2']);
    }

    public function testGenerateKeyPair_ShouldGenerateConsistentSubkeysForSameKeyAndMessageLength()
    {
        $key = 'saju.cse@hstu';
        $messageLength = 16;

        $result1 = $this->keyGenerator->generateKeyPair($messageLength, $key, $this->state);
        $result2 = $this->keyGenerator->generateKeyPair($messageLength, $key, $this->state);

        $this->assertEquals($result1['subkey1'], $result2['subkey1']);
        $this->assertEquals($result1['subkey2'], $result2['subkey2']);
    }

    public function testGenerateKeyPair_ShouldHandleKeyLengthSmallerThanMessageLength()
    {
        $key = 'cse16';
        $messageLength = 16;

        $result = $this->keyGenerator->generateKeyPair($messageLength, $key, $this->state);

        $this->assertNotEmpty($result['subkey1']);
        $this->assertNotEmpty($result['subkey2']);
    }

    public function testGenerateKeyPair_ShouldHandleKeyLengthLargerThanMessageLength()
    {
        $key = 'saju.cse.hstu@lynkto.net';
        $messageLength = 10;

        $result = $this->keyGenerator->generateKeyPair($messageLength, $key, $this->state);

        $this->assertNotEmpty($result['subkey1']);
        $this->assertNotEmpty($result['subkey2']);
    }

    public function testGenerateKeyPair_ShouldThrowExceptionForEmptyKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Key cannot be empty');

        $this->keyGenerator->generateKeyPair(16, '', $this->state);
    }

    public function testGenerateKeyPair_ShouldApplyLengthEnhancementCorrectly()
    {
        $key = 'saju@1932';
        $messageLength = 20;

        $result = $this->keyGenerator->generateKeyPair($messageLength, $key, $this->state);

        $this->assertTrue(strlen($result['subkey2']) % 8 === 0);
    }

    public function testGenerateKeyPair_ShouldProduceDifferentSubkeysForDifferentKeys()
    {
        $key1 = 'BSK-Online';
        $key2 = 'cipher-bsk';
        $messageLength = 16;

        $result1 = $this->keyGenerator->generateKeyPair($messageLength, $key1, $this->state);
        $result2 = $this->keyGenerator->generateKeyPair($messageLength, $key2, $this->state);

        $this->assertNotEquals($result1['subkey1'], $result2['subkey1']);
    }

    public function testGenerateKeyPair_ShouldHandleRandomIndexingWithVaryingMessageLength()
    {
        $key = 'Hstu@5200';
        $messageLength1 = 16;
        $messageLength2 = 20;

        $result1 = $this->keyGenerator->generateKeyPair($messageLength1, $key, $this->state);
        $result2 = $this->keyGenerator->generateKeyPair($messageLength2, $key, $this->state);

        $this->assertNotEquals($result1['subkey1'], $result2['subkey1']);
    }
}