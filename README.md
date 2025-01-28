# cipher-bsk-composer-package
Bornomala Symmetric Key (BSK) stream cipher, is a lightweight encryption library implementing techniques from the research paper 'A Hybrid Cryptographic Scheme of Modified Vigenère Cipher using Randomized Approach for Enhancing Data Security' by Sazzad Saju. This library aims to provide robust data security with an easy-to-use API. For more details, visit: https://bit.ly/cipher-bsk

## Installation/Launch

Install the package via Composer:

```bash
composer require sham3r/cipher-bsk
```


## Usage
To use in your project get the encrypt and decrypt function from object deconstruction

```
<?php
    require 'vendor/autoload.php';
    use CipherBsk\CipherBsk;

    $message = "Hajee Mohammad Danesh Science and Technology University";
    $key = "Hstu@5200";

    $encryptedMessage = $cipherBsk->encrypt($message, $key);
    echo "Encrypted Message: $encryptedMessage\n";
    
    $decryptedMessage = $cipherBsk->decrypt($encryptedMessage, $key);
    echo "Decrypted Message: $decryptedMessage\n";
    
    // Example output:
    // Encrypted Message: 532F395A5A3F7C773B322F3E3C2E4D74675C6F7B5B4B39775A2667666C737A341025276556267441424B783F6C4F5F754B45645071777933
    // Decrypted Message: Hajee Mohammad Danesh Science and Technology University
```

## Features
- Avalanche effect 
- Immune from frequency analysis attack
- Output ranges from ASCII(0-255)

## Documentations
- Published paper: [DOI: 10.5120/ijca2021921290](https://www.ijcaonline.org/archives/volume183/number2/31897-2021921290)
- Online tool: [BSK ONLINE](https://sazzad-saju.github.io/BSK-Online/)




## Run the unit test: 

> vendor/bin/phpunit tests

> vendor/bin/phpunit tests/KeyGeneratorTest.php --testdox

> vendor/bin/phpunit tests/KeyGeneratorTest.php --testdox --debug

> php tests/RandomDebugTest.php

> vendor/bin/phpunit --testdox


## Unit Test Flags: 

* Each . represents a passing test.
* An F represents a failing test.
* An E represents an error.
* An R would represent a risky test (e.g., no assertions in the test).