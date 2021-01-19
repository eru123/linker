 - Back to [main page](index)
# Encryption
Linker encryption class comes in handy on two-way encryption
```php
use Linker\Crypt\Core as Crypt;

$text = "Hello there!";
```
## LZW
LZW (Lempel-Ziv-Welch) a popular compression method
### LZW Compress
```php
$encrypt = Crypt::lzw_compress($text);
// returns binary
```
### LZW Decompress
```php
$decrypt = Crypt::lzw_decompress($encrypt);
// returns a string "Hello there!"
```
## Blowfish
A symmetric-key block cipher
```php
use Linker\Crypt\Core as Crypt; 

$key = "some_private_key";
```
### Blowfish Encrypt
```php
$encrypt = Crypt::blow($text,$key);
// returns binary;
```
### Blowfish Decrypt
```php
$decrypt = Crypt::blow($encrypt,$key);
// returns a string "Hello there!"
```

## Blowfish + Base64
Combination of Blowfish and base64 encryption
```php
use Linker\Crypt\Core as Crypt; 

$key = "some_private_key";
```
### Blowfish + Base64 Encrypt
```php
$encrypt = Crypt::encode($text,$key);
// returns a string "OwoBCTBQBgETExFE";
```
### Blowfish + Base64 Decrypt
```php
$decrypt = Crypt::decode($encrypt,$key);
// returns a string "Hello there!"
```
