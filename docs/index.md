# Installation
### Via composer
```bash
composer require eru123/linker
```
# Usage
Include the composer autoload to your project
```php
<?php

require_once __DIR__."/vendor/autoload.php";

```
# Array SearchRow
SearchRow is mainly made for proccessing array on an SQL like array schema.
```php
use Linker\Array\SearchRow;

/**
 * can be use only in the specified multidimensional array format.
 * array(
 *     array(
 *         "id" => 324,
 *         "key" => "value"
 *     ),
 *     array(
 *         "id" => 324,
 *         "key" => "value"
 *     )
 * )
 */

// Sample Data
$data = [
    [
        "id" => "1",
        "firstname" => "jericho",
        "lastname" => "aquino"
    ],
    [
        "id" => "2",
        "firstname" => "eru",
        "lastname" => "roraito"
    ],
    [
        "id" => "3",
        "firstname" => "koko",
        "lastname" => "kwekkwak"
    ]
]
```
## Like
SearchRow Like is used to search a data using regular expression
### Like - Single query
```php
$regex = "/er/i";
$column = "firstname";
$use_new_key = FALSE; // set to true if you want to re-keys the elements of $data in the result
$result = SearchRow::like($regex,$column,$data,$use_new_key);
/**
 * $result = 
 * [
 *      [
 *          "id" => "1",
 *          "firstname" => "jericho",
 *          "lastname" => "aquino"
 *      ],
 *      [
 *          "id" => "2",
 *          "firstname" => "eru",
 *          "lastname" => "roraito"
 *      ]
 * ];
*/
```
### Like - Multiple query
```php
$regex = [
    "/er/i",
    "/koko/i"
];
$column = "firstname";
$use_new_key = FALSE; // set to true if you want to re-keys the elements of $data in the result
$result = SearchRow::mutiLike($regex,$column,$data,$use_new_key);
/**
 * $result = 
 * [
 *      [
 *          "id" => "1",
 *          "firstname" => "jericho",
 *          "lastname" => "aquino"
 *      ],
 *      [
 *          "id" => "2",
 *          "firstname" => "eru",
 *          "lastname" => "roraito"
 *      ],
 *      [
 *          "id" => "3",
 *          "firstname" => "koko",
 *          "lastname" => "kwekkwak"
 *      ]
 * ];
*/
```
## Exact
SearchRow Exact is used to search a data with exact value as the query
### Exact - Single query
```php
$search = "jericho";
$column = "firstname";
$use_new_key = FALSE; // set to true if you want to re-keys the elements of $data in the result
$result = SearchRow::exact($search,$column,$data,$use_new_key);
/**
 * $result = 
 * [
 *      [
 *          "id" => "1",
 *          "firstname" => "jericho",
 *          "lastname" => "aquino"
 *      ]
 * ];
*/
```
### Exact - Multiple query
```php
$search = [
    "jericho",
    "eru"
];
$column = "firstname";
$use_new_key = FALSE; // set to true if you want to re-keys the elements of $data in the result
$result = SearchRow::mutiExact($search,$column,$data,$use_new_key);
/**
 * $result = 
 * [
 *      [
 *          "id" => "1",
 *          "firstname" => "jericho",
 *          "lastname" => "aquino"
 *      ],
 *      [
 *          "id" => "2",
 *          "firstname" => "eru",
 *          "lastname" => "roraito"
 *      ]
 * ];
*/
```
## Not
SearchRow Not is opposite to Search Exact, it searches all data that not have the same value as the query
### Not - Single query
```php
$search = "koko";
$column = "firstname";
$use_new_key = FALSE; // set to true if you want to re-keys the elements of $data in the result
$result = SearchRow::not($search,$column,$data,$use_new_key);
/**
 * $result = 
 * [
 *      [
 *          "id" => "1",
 *          "firstname" => "jericho",
 *          "lastname" => "aquino"
 *      ],
 *      [
 *          "id" => "2",
 *          "firstname" => "eru",
 *          "lastname" => "roraito"
 *      ]
 * ];
*/
```
### Not - Multiple query
```php
$search = [
    "koko",
    "jericho"
];
$column = "firstname";
$use_new_key = FALSE; // set to true if you want to re-keys the elements of $data in the result
$result = SearchRow::mutiNot($search,$column,$data,$use_new_key);
/**
 * $result = 
 * [
 *      [
 *          "id" => "2",
 *          "firstname" => "eru",
 *          "lastname" => "roraito"
 *      ]
 * ];
*/
```

# Encryption
Linker encryption class comes in handy on two-way encryption
```php
use Linker\Crypt\Core as Crypt;

$text = "Hello there!";
```
## LZW
LZW (Lempel-Ziv-Welch) a popular compression 
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
$text = "Hello there!";
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
$text = "Hello there!";
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