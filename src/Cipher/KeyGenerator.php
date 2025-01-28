<?php

namespace CipherBsk\Cipher;

use CipherBsk\State;
use CipherBsk\Utils\Helpers;

class KeyGenerator
{
    public function generateKeyPair(int $messageLength, string $key, State $state): array
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key cannot be empty');
        }

        $subkey1 = '';
        $keyLength = strlen($key);

        // Text to Int
        $grd = 1;
        $countK = 0;
        for ($i = 0; $i < $keyLength; $i++) {
            $countK += ord($key[$i]) * $grd;
            $grd *= 2;
        }
        $countK += $messageLength;

        // Key Enhancement
        if ($messageLength > $keyLength) {
            $rep = intdiv($messageLength, $keyLength);
            $add = $messageLength - ($rep * $keyLength);
            $subkey1 = str_repeat($key, $rep);

            $i = 0;
            while ($add > 0) {
                $subkey1 .= $key[$i++];
                $add--;
            }
        } else {
            $messageLength = $keyLength;
            $subkey1 = $key;
        }

        // Key Substitution
        for ($i = 0; $i < $messageLength; $i++) {
            $numb = ord($subkey1[$i]);
            $countK = ($numb + $countK) % 95;
            $numb += $countK;
            if ($numb > 126) {
                $numb -= 95;
            }
            $subkey1 = Helpers::replaceAt($subkey1, $i, chr($numb));
        }
        $countK += $messageLength;

        // Random Indexing
        srand(ord($subkey1[0]));
        
        for ($i = 0; $i < $messageLength; $i++) {
            $countK = ord($subkey1[$i]);
            while ($countK > 10) {
                $countK %= 10;
            }
            $max = pow(10, $countK);
            $state->setMax($max);
            
            $randomNumber = rand(0, $max - 1);
            $state->setRand($randomNumber);
            $pos = $randomNumber % $messageLength;

            $temp = $subkey1[$i];
            $subkey1[$i] = $subkey1[$pos];
            $subkey1[$pos] = $temp;
        }

        // Length Enhancement
        $i = 0;
        $subkey2 = $subkey1;
        while (strlen($subkey2) % 8 != 0) {
            $subkey2 .= $subkey1[$i++];
        }

        // Generate Subkey2
        $rndNum = (int) ($state->getRand() * $state->getMax());
        for ($i = 0; $i < strlen($subkey2); $i++) {
            $numb = ord($subkey2[$i]);
            $rndNum = ($numb + $rndNum) % 95;
            $numb += $rndNum;
            if ($numb > 126) {
                $numb -= 95;
            }
            $subkey2 = Helpers::replaceAt($subkey2, $i, chr($numb));
        }
        
        return [
            'subkey1' => $subkey1,
            'subkey2' => $subkey2
        ];
    }
}
