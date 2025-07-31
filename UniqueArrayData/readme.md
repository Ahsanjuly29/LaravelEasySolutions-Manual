
---

# Array Unique in PHP

Remove duplicate values from an array with `array_unique()`.

## Example

```php
$a = array("a" => "red", "b" => "green", "c" => "red");
$a = array_unique($a);
```

## Optional Flags

* `SORT_STRING` (default)
* `SORT_REGULAR`
* `SORT_NUMERIC`
* `SORT_LOCALE_STRING`

Example with flag:

```php
$a = array_unique($a, SORT_NUMERIC);
```

---

 