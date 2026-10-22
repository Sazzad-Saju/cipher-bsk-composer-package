# Cipher BSK — PHP and Laravel Encryption Package

**`sham3r/cipher-bsk`** is the Composer package for **Cipher BSK (Bornomala Symmetric Key)**, developed by **Sazzad Hossain Saju**. It brings the cipher model, originally implemented in C++, to PHP applications through a reusable API for text encryption and decryption.

The package works in standalone PHP projects and integrates with Laravel through configuration, a service provider, and a facade. Version 1.1.1 added `secureHash()` and `verifyHash()`, combining bcrypt password hashing with Cipher BSK encryption while retaining the existing `encrypt()` and `decrypt()` methods.

**[View on Packagist](https://packagist.org/packages/sham3r/cipher-bsk) · [Try Cipher BSK Web](https://sazzad-saju.github.io/cipher-bsk-web/) · [Read the project story on Medium](https://medium.com/@sham3r/cipher-bsk-from-a-simple-experiment-to-practical-solutions-my-passionate-journey-in-cryptography-d5de4e83add5)**

## Research Background

The underlying cipher model is documented in **[A Hybrid Cryptographic Scheme of Modified Vigenère Cipher using Randomized Approach for Enhancing Data Security](https://www.ijcaonline.org/archives/volume183/number2/31897-2021921290/)**, authored by **Sazzad Hossain Saju, Sayed Mahmudul Haque, and Liakot Hossain Lingcon**.

Published in the *International Journal of Computer Applications*, **Volume 183, Number 2, May 2021, pages 1–8**, the paper explores a modified Vigenère-based symmetric cipher using key generation, substitution, rearrangement, and padding. It combines that cipher with RSA to encrypt the symmetric key for its intended recipient.

**DOI:** [10.5120/ijca2021921290](https://doi.org/10.5120/ijca2021921290) · **[Find the paper on Google Scholar](https://scholar.google.com/scholar?q=A+Hybrid+Cryptographic+Scheme+of+Modified+Vigenere+Cipher+using+Randomized+Approach+for+Enhancing+Data+Security)**

This Composer package implements the PHP Cipher BSK component and adds application-level helpers and Laravel integration. The password helpers are later package features; RSA and the planned image-steganography integration are separate parts of the wider project.

The paper documents the experimental design and its analysis. Publication and observed output behavior do not establish blanket immunity to cryptographic attacks.

If you use or build on this model in your research, please cite the original paper.

## Package at a Glance

| Capability | API or integration |
| --- | --- |
| Text encryption and decryption | `encrypt($message, $key)` and `decrypt($ciphertext, $key)` |
| Password protection and verification | `secureHash($password)` and `verifyHash($password, $storedValue)` |
| Standalone PHP usage | Composer autoloading and the `CipherBsk\\CipherBsk` class |
| Laravel usage | Auto-discovery, facade, service container, and published configuration |
| Key configuration for password helpers | `CIPHER_BSK_KEY` with `APP_KEY` as fallback |

### 🔐 New in v1.1.1
- Added `secureHash()` and `verifyHash()` for dual-layer password encryption.
- Added Laravel integration (config, service provider, and facade).
- Backward compatible with v1.0.

## Installation/Launch

Install the package via Composer:

```bash
composer require sham3r/cipher-bsk
```

## Usage

### Basic Encryption/Decryption (Existing functionality)

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

### 🆕 Secure Password Hashing (v1.1.1)

**Note:** You must set an environment variable for secure hashing:

```php
<?php
require 'vendor/autoload.php';
use CipherBsk\CipherBsk;

// Set environment variable for key (REQUIRED)
putenv('CIPHER_BSK_KEY=Hstu@5200');
// OR use APP_KEY
putenv('APP_KEY=your-application-key');

$cipherBsk = new CipherBsk();

// Hash a password with dual-layer encryption
$password = "mysecretpassword";
$hashedPassword = $cipherBsk->secureHash($password);
echo "Hashed Password: $hashedPassword\n";

// Verify password
$isValid = $cipherBsk->verifyHash($password, $hashedPassword);
echo "Password Valid: " . ($isValid ? "Yes" : "No") . "\n";

// Wrong password verification
$isInvalid = $cipherBsk->verifyHash("wrongpassword", $hashedPassword);
echo "Wrong Password Valid: " . ($isInvalid ? "Yes" : "No") . "\n";
```

### 🆕 Laravel Integration (v1.1.1)

#### Auto-Discovery Setup
The package automatically registers itself in Laravel applications. No manual setup required!

#### Using the Facade:
```php
<?php
use CipherBsk;

// Basic encryption/decryption
$message = "Hello, World!";
$key = "Hstu@5200";

$encrypted = CipherBsk::encrypt($message, $key);
$decrypted = CipherBsk::decrypt($encrypted, $key);

// Secure password hashing (uses your Laravel config)
$hash = CipherBsk::secureHash('Hstu@5200');
dd(CipherBsk::verifyHash('Hstu@5200', $hash)); // true

$wrongHash = CipherBsk::verifyHash('wrongpassword', $hash);
dd($wrongHash); // false
```

#### Laravel Configuration:

1. **Publish the config file (optional):**
```bash
php artisan vendor:publish --tag=config
```

2. **Set your environment variables in `.env`:**
```env
# Option 1: Use dedicated cipher key
CIPHER_BSK_KEY=your-secret-cipher-key

# Option 2: Use your existing APP_KEY (fallback)
APP_KEY=base64:your-laravel-app-key
```

3. **Configuration file (`config/cipher-bsk.php`):**
```php
<?php
return [
    'key' => env('CIPHER_BSK_KEY', env('APP_KEY')),
];
```

#### Laravel Service Container:
```php
<?php
// Inject via service container
class UserController extends Controller
{
    public function hashPassword(Request $request, CipherBsk $cipher)
    {
        $password = $request->input('password');
        $hash = $cipher->secureHash($password);
        
        // Store $hash in database
        User::create(['password' => $hash]);
        
        return response()->json(['success' => true]);
    }
    
    public function verifyPassword(Request $request, CipherBsk $cipher)
    {
        $user = User::find($request->user_id);
        $inputPassword = $request->input('password');
        
        $isValid = $cipher->verifyHash($inputPassword, $user->password);
        
        return response()->json(['valid' => $isValid]);
    }
}
```

## Features
- **Avalanche effect** - Small changes in input create large changes in output
- **Immune from frequency analysis attack** - Secure against cryptographic attacks
- **Output ranges from ASCII(0-255)** - Full character space utilization
- 🆕 **Dual-layer password hashing** (CipherBSK + bcrypt) - Enhanced security
- 🆕 **Laravel integration** with auto-discovery - Seamless framework integration
- 🆕 **Environment-based key management** - Secure configuration
- ✅ **Backward compatible** with existing code - No breaking changes

## Important Security Notes

### For Secure Hashing Features
- **MUST** set `CIPHER_BSK_KEY` or `APP_KEY` environment variable
- **Never** commit keys to version control
- **Use** different keys for different environments (dev, staging, production)

### Example Environment Setup:
```bash
# In your .env file
CIPHER_BSK_KEY=your-long-random-secret-key-here

# Or use existing Laravel APP_KEY
APP_KEY=base64:generated-laravel-key
```

### Error Handling:
If no key is configured, the package will throw a `RuntimeException` with helpful instructions:
```
No encryption key configured. Please set one of the following environment variables: 
CIPHER_BSK_KEY or APP_KEY. 
For Laravel projects: run "php artisan vendor:publish --tag=config" to publish the config file.
```


## Documentation
- **Published paper:** [DOI: 10.5120/ijca2021921290](https://www.ijcaonline.org/archives/volume183/number2/31897-2021921290)
- **Online tool:** [Cipher BSK Web](https://sazzad-saju.github.io/cipher-bsk-web/)
- **GitHub Repository:** [cipher-bsk-composer-package](https://github.com/Sazzad-Saju/cipher-bsk-composer-package)

## The Cipher BSK Ecosystem

This package is part of a series of open-source implementations and applications built around the same cipher model:

- **[C++ console application](https://github.com/Sazzad-Saju/BSK-Stream-Cipher):** the original symmetric cipher implementation.
- **[RSA component in Python](https://github.com/Sazzad-Saju/RSA_Algorithm):** public-key encryption of the Cipher BSK symmetric key.
- **[JavaScript npm package](https://github.com/Sazzad-Saju/cipher-bsk-npm-package):** reusable encryption and decryption for JavaScript applications.
- **[Cipher BSK Web](https://github.com/Sazzad-Saju/cipher-bsk-web):** a browser interface for experimenting with text encryption.
- **[Browser encryption extension](https://github.com/Sazzad-Saju/cipher-bsk-extension)** and **[password manager extension](https://github.com/Sazzad-Saju/cipherbsk-pass-manager-ext)**.

The planned image-steganography extension would embed both the BSK-encrypted message and the RSA-encrypted symmetric key inside an image. It is not included in this Composer package.

[Read how the university experiment developed into this ecosystem on Medium](https://medium.com/@sham3r/cipher-bsk-from-a-simple-experiment-to-practical-solutions-my-passionate-journey-in-cryptography-d5de4e83add5).

## Testing

### Run All Tests:
```bash
vendor/bin/phpunit --testdox
```

### Test Specific Features:
```bash
# Test basic encryption/decryption
vendor/bin/phpunit tests/CipherBskTest.php --testdox

# Test secure hashing features
vendor/bin/phpunit tests/SecureHashTest.php --testdox

# Test key generation
vendor/bin/phpunit tests/KeyGeneratorTest.php --testdox
```

### Test Coverage:
- **25 tests total** - Comprehensive coverage
- **71 assertions** - Thorough validation
- **Core encryption** - Original CipherBSK algorithm
- **Secure hashing** - New dual-layer password security
- **Environment handling** - Key resolution and error cases
- **Laravel integration** - Framework-specific features

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests (`vendor/bin/phpunit`)
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Authors

- **Sazzad Saju** - *Initial work and research* - [GitHub](https://github.com/Sazzad-Saju)
- Email: saju.cse.hstu@gmail.com

## Acknowledgments

- Based on the research paper: "A Hybrid Cryptographic Scheme of Modified Vigenère Cipher using Randomized Approach for Enhancing Data Security"
- Hajee Mohammad Danesh Science and Technology University
- Laravel community for framework integration patterns
