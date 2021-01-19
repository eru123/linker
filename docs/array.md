 - Back to [main page](index)
# Array
To proccess complex array problems
# SearchRow
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
];
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
