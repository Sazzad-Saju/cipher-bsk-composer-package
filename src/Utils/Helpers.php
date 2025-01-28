<?php

namespace CipherBsk\Utils;

class Helpers
{
    // Replace character at a specific index in a string
    public static function replaceAt(string $str, int $index, string $newChar): string
    {
        $length = strlen($str);
        if ($index < 0 || $index >= $length) {
            throw new \InvalidArgumentException("Index is out of bounds");
        }
        return substr($str, 0, $index) . $newChar . substr($str, $index + 1);
    }

    // Swap two characters in a string
    public static function swapStr(string $str, int $first, int $last): string
    {
        $length = strlen($str);
        if ($first < 0 || $last < 0 || $first >= $length || $last >= $length) {
            throw new \InvalidArgumentException("Indexes are out of bounds");
        }

        $strArray = str_split($str);
        $temp = $strArray[$first];
        $strArray[$first] = $strArray[$last];
        $strArray[$last] = $temp;

        return implode('', $strArray);
    }

    // Calculate padding characters in a string
    public static function padBytes(string $hexString): int
    {
        $pad = 0;
        $chunks = str_split($hexString, 2);

        foreach ($chunks as $chunk) {
            $val = hexdec($chunk);
            if ($val < 32 || $val > 125) {
                $pad++;
            }
        }

        return $pad;
    }
}