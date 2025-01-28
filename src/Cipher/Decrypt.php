<?php

namespace CipherBsk\Cipher;

use CipherBsk\State;

class Decrypt
{
    private KeyGenerator $keyGenerator;

    public function __construct()
    {
        $this->keyGenerator = new KeyGenerator();
    }

    public function decrypt(string $ciphertext, string $key, State $state): string
    {
        if (empty($ciphertext) || empty($key)) {
            return '';
        }

        $messageLength = strlen($ciphertext) / 2;
        $pad = 0;

        // Hexadecimal to ASCII conversion
        $ciphertext = str_split($ciphertext, 2);
        foreach ($ciphertext as &$chunk) {
            $val = hexdec($chunk);
            if ($val < 32 || $val > 125) {
                $chunk = '~';
                $pad++;
            } else {
                $chunk = chr($val);
            }
        }

        // Original message length
        $messageLength -= $pad;

        // Generate keypair
        $state->reset();
        $keyGen = $this->keyGenerator->generateKeyPair($messageLength, $key, $state);

        // Key-triggered reverse indexing: subkey2
        foreach ($ciphertext as $i => &$char) {
            $t = ord($keyGen['subkey2'][$i]);
            $j = $t % count($ciphertext);
            [$ciphertext[$i], $ciphertext[$j]] = [$ciphertext[$j], $ciphertext[$i]];
        }

        // Shrink length
        $ciphertext = array_slice($ciphertext, 0, $messageLength);

        // Subkey1 operation
        foreach ($ciphertext as $i => &$char) {
            $mv = ord($char);
            $sk1v = ord($keyGen['subkey1'][$i]);
            $pos = $mv - $sk1v;
            while ($pos < 32) {
                $pos += 94;
            }
            $char = chr($pos);
        }

        return implode('', $ciphertext);
    }
}
