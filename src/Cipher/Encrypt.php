<?php

namespace CipherBsk\Cipher;

use CipherBsk\State;

class Encrypt
{
    private KeyGenerator $keyGenerator;

    public function __construct()
    {
        $this->keyGenerator = new KeyGenerator();
    }

    public function encrypt(string $message, string $key, State $state): string
    {
        
        if (empty($message) || empty($key)) {
            return '';
        }
        
        // Generate keypair
        $state->reset();
        $keyGen = $this->keyGenerator->generateKeyPair(strlen($message), $key, $state);

        // Subkey1 operation
        $message = str_split($message);
        foreach ($message as $i => &$char) {
            $mv = ord($char);
            if ($mv == 10) {
                $mv = 126;
            }
            $sk1v = ord($keyGen['subkey1'][$i]);
            $pos = ($mv + $sk1v) % 94;
            if ($pos < 32) {
                $pos += 94;
            }
            $char = chr($pos);
        }

        // Padding
        while (count($message) % 8 != 0) {
            $message[] = '~';
        }
        
        // Key-triggered indexing: subkey2
        for ($i = count($message) - 1; $i >= 0; $i--) {
            $t = ord($keyGen['subkey2'][$i]);
            $j = $t % count($message);
            [$message[$i], $message[$j]] = [$message[$j], $message[$i]];
        }
        
        // Hexadecimal conversion
        foreach ($message as $i => &$char) {
            $char = str_pad(dechex(ord($char)), 2, '0', STR_PAD_LEFT);
            if ($char === '7e') {
                if ($i % 2 === 0) {
                    $temp = (int) ($state->getRand() * $state->getMax()) % 32;
                    $char = str_pad(dechex($temp), 2, '0', STR_PAD_LEFT);
                } else {
                    $numb = (int) ($state->getRand() * $state->getMax()) % 256;
                    if ($numb < 126) {
                        $numb += 130;
                    }
                    $char = str_pad(dechex($numb), 2, '0', STR_PAD_LEFT);
                }
            }
        }
        
        return strtoupper(implode('', $message));
    }
}
