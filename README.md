# cipher-bsk-composer-package
Bornomala Symmetric Key (BSK) stream cipher, is a lightweight encryption library implementing techniques from the research paper 'A Hybrid Cryptographic Scheme of Modified Vigenère Cipher using Randomized Approach for Enhancing Data Security' by Sazzad Saju. This library aims to provide robust data security with an easy-to-use API. For more details, visit: https://bit.ly/cipher-bsk

### 🔐 New in v1.5.0
- Added `secureHash()` and `verifyHash()` for dual-layer password encryption.
- Added Laravel integration (config, service provider, and facade).
- Backward compatible with v1.0.

## Installation/Launch

Install the package via Composer:

```bash
composer require sham3r/cipher-bsk
```


## Usage
To use in your project get the encrypt and decrypt function from object deconstruction

### Basic Encryption/Decryption

```php
<?php
    require 'vendor/autoload.php';
    use CipherBsk\CipherBsk;

    $message = "Hajee Mohammad Danesh Science and Technology University";
    $key = "Hstu@5200";
    
    $cipherBsk = new CipherBsk();

    $encryptedMessage = $cipherBsk->encrypt($message, $key);
    echo "Encrypted Message: $encryptedMessage\n";
    
    $decryptedMessage = $cipherBsk->decrypt($encryptedMessage, $key);
    echo "Decrypted Message: $decryptedMessage\n";
    
    // Example output:
    // Encrypted Message: 7B4D5249784F285E6433232166594C6A5A5A3322532A7C4E4F72697D2B4C52515F7400753644674061576C254E47724635612920714C6A21
    // Decrypted Message: Hajee Mohammad Danesh Science and Technology University
```

### 🆕 Secure Password Hashing (v1.5.0)

```php
<?php
    require 'vendor/autoload.php';
    use CipherBsk\CipherBsk;

    // Set environment variable for key (recommended)
    putenv('CIPHER_BSK_KEY=your-secret-cipher-key');
    
    $cipherBsk = new CipherBsk();

    // Hash a password with dual-layer encryption
    $password = "my-secure-password";
    $hashedPassword = $cipherBsk->secureHash($password);
    echo "Hashed Password: $hashedPassword\n";
    
    // Verify password
    $isValid = $cipherBsk->verifyHash($password, $hashedPassword);
    echo "Password Valid: " . ($isValid ? "Yes" : "No") . "\n";
    
    // Wrong password verification
    $isInvalid = $cipherBsk->verifyHash("wrong-password", $hashedPassword);
    echo "Wrong Password Valid: " . ($isInvalid ? "Yes" : "No") . "\n";
```

### 🆕 Laravel Integration (v1.5.0)

#### Using Facade:
```php
use CipherBsk;

// Basic encryption
$encrypted = CipherBsk::encrypt($message, $key);
$decrypted = CipherBsk::decrypt($encrypted, $key);

// Secure hashing (uses config/cipher-bsk.php key)
$hash = CipherBsk::secureHash('password123');
$isValid = CipherBsk::verifyHash('password123', $hash);
```

#### Configuration:
Create `config/cipher-bsk.php`:
```php
<?php
return [
    'key' => env('CIPHER_BSK_KEY', env('APP_KEY')),
];
```

## Features
- Avalanche effect 
- Immune from frequency analysis attack
- Output ranges from ASCII(0-255)
- 🆕 **Dual-layer password hashing** (CipherBSK + bcrypt)
- 🆕 **Laravel integration** with auto-discovery
- 🆕 **Environment-based key management**
- ✅ **Backward compatible** with existing code

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