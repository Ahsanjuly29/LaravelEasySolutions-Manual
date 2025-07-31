 
---

# How to Get Current URL or Its Segments in Laravel

## Examples

**Example 1: Get the full current URL**

```php
Request::url()->current();
```

---

**Example 2: Get a specific segment of the URL**

```php
request()->segment(number);
```

Replace `number` with the part of the URL you want to get. For example, `1` for the first segment after the domain, `2` for the second, and so on.

---

## Notes

* Use **Example 1** to get the complete URL.
* Use **Example 2** to get a specific segment.
* URL segments are the parts separated by `/`.
 