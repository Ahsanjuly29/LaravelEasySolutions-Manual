Sure! Below is a `README.md` formatted in a tutorial style with SEO-friendly questions and answers. This can serve as a guide for you to refer back to whenever you need it in the future.

---

# PHP Tutorial: How to Optimize SQL Query and Measure Memory Usage

## Table of Contents

- [PHP Tutorial: How to Optimize SQL Query and Measure Memory Usage](#php-tutorial-how-to-optimize-sql-query-and-measure-memory-usage)
  - [Table of Contents](#table-of-contents)
  - [How to fix SQL syntax issues with multiple UNION queries in Laravel?](#how-to-fix-sql-syntax-issues-with-multiple-union-queries-in-laravel)
    - [Issue:](#issue)
    - [Solution:](#solution)
  - [How to execute raw SQL queries and return results in Laravel?](#how-to-execute-raw-sql-queries-and-return-results-in-laravel)
    - [Issue:](#issue-1)
    - [Solution:](#solution-1)
  - [How to check memory usage for a SQL query result in PHP?](#how-to-check-memory-usage-for-a-sql-query-result-in-php)
    - [Issue:](#issue-2)
    - [Solution:](#solution-2)
  - [How to display memory usage in MB or GB based on the result size in PHP?](#how-to-display-memory-usage-in-mb-or-gb-based-on-the-result-size-in-php)
    - [Issue:](#issue-3)
    - [Solution:](#solution-3)
  - [Final Solution and Code Example](#final-solution-and-code-example)

---

## How to fix SQL syntax issues with multiple UNION queries in Laravel?

### Issue:
When using the `UNION` operator in SQL queries with Laravel's `DB::select()`, it's common to run into errors, such as mismatched column counts or syntax issues. In your case, the query returned an error due to inconsistent column selection.

### Solution:
Ensure that every `SELECT` in a `UNION` query returns the same number of columns with consistent names.

```php
$datahotel = DB::select(DB::raw('
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT cityLongName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT name, address FROM hotels_b2b
    JOIN hotels_b2b_additional_info ON hotels_b2b.HotelID = hotels_b2b_additional_info.hotelid
    WHERE hotels_b2b_additional_info.selectStatus != 2
    AND hotels_b2b_additional_info.selectStatus != "")
    UNION
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT stateCode AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT destinationName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT name, NULL AS address FROM cities WHERE deleted_at IS NULL)
    UNION
    (SELECT name, NULL AS address FROM states WHERE deleted_at IS NULL)
    UNION
    (SELECT name, address FROM hotels WHERE deleted_at IS NULL)
'));
```

---

## How to execute raw SQL queries and return results in Laravel?

### Issue:
You might face confusion about how to execute raw SQL queries and retrieve the results in Laravel using the `DB` facade.

### Solution:
Use `DB::select()` to execute a raw SQL query. This method returns an array of results directly.

```php
$datahotel = DB::select(DB::raw('
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT cityLongName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT name, address FROM hotels_b2b
    JOIN hotels_b2b_additional_info ON hotels_b2b.HotelID = hotels_b2b_additional_info.hotelid
    WHERE hotels_b2b_additional_info.selectStatus != 2
    AND hotels_b2b_additional_info.selectStatus != "")
    UNION
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT stateCode AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT destinationName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT name, NULL AS address FROM cities WHERE deleted_at IS NULL)
    UNION
    (SELECT name, NULL AS address FROM states WHERE deleted_at IS NULL)
    UNION
    (SELECT name, address FROM hotels WHERE deleted_at IS NULL)
'));
```

This will return the query result as an array that you can manipulate as needed.

---

## How to check memory usage for a SQL query result in PHP?

### Issue:
When handling large datasets in PHP, you might want to monitor the memory usage to ensure that your application does not exceed PHP's memory limits.

### Solution:
You can use PHP's `memory_get_usage()` function to check how much memory your application is consuming before and after storing query results. 

```php
// Get initial memory usage
$initialMemory = memory_get_usage();

// Execute the query and store the result
$datahotel = DB::select(DB::raw('
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT cityLongName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT name, address FROM hotels_b2b
    JOIN hotels_b2b_additional_info ON hotels_b2b.HotelID = hotels_b2b_additional_info.hotelid
    WHERE hotels_b2b_additional_info.selectStatus != 2
    AND hotels_b2b_additional_info.selectStatus != "")
    UNION
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT stateCode AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT destinationName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT name, NULL AS address FROM cities WHERE deleted_at IS NULL)
    UNION
    (SELECT name, NULL AS address FROM states WHERE deleted_at IS NULL)
    UNION
    (SELECT name, address FROM hotels WHERE deleted_at IS NULL)
'));

// Get memory usage after storing the result
$finalMemory = memory_get_usage();
$memoryUsed = $finalMemory - $initialMemory;
echo "Memory used: " . number_format($memoryUsed) . " bytes";
```

---

## How to display memory usage in MB or GB based on the result size in PHP?

### Issue:
If the memory usage is large, displaying it in bytes can be overwhelming. Instead, we want to display the result in either **MB** or **GB** depending on the size.

### Solution:
We can convert the memory usage into MB if it’s less than 1 GB, or GB if it's larger.

```php
// Get initial memory usage
$initialMemory = memory_get_usage();

// Execute the query and store the result
$datahotel = DB::select(DB::raw('
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT cityLongName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT name, address FROM hotels_b2b
    JOIN hotels_b2b_additional_info ON hotels_b2b.HotelID = hotels_b2b_additional_info.hotelid
    WHERE hotels_b2b_additional_info.selectStatus != 2
    AND hotels_b2b_additional_info.selectStatus != "")
    UNION
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT stateCode AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT destinationName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT name, NULL AS address FROM cities WHERE deleted_at IS NULL)
    UNION
    (SELECT name, NULL AS address FROM states WHERE deleted_at IS NULL)
    UNION
    (SELECT name, address FROM hotels WHERE deleted_at IS NULL)
'));

// Get memory usage after storing the result
$finalMemory = memory_get_usage();
$memoryUsed = $finalMemory - $initialMemory;

// Convert to MB or GB
if ($memoryUsed >= 1024 * 1024 * 1024) {
    $memoryUsed = number_format($memoryUsed / (1024 * 1024 * 1024), 2) . ' GB';
} else {
    $memoryUsed = number_format($memoryUsed / (1024 * 1024), 2) . ' MB';
}

// Output the memory used
echo "Memory used: " . $memoryUsed;
```

This code will:
1. Check the memory usage.
2. Display the memory used in either **MB** or **GB**, depending on the size.

---

## Final Solution and Code Example

The complete solution ensures:
1. **Proper SQL Syntax**: Fixed the `UNION` clause and ensured all `SELECT` statements returned consistent columns.
2. **Memory Usage Monitoring**: Tracked memory usage before and after executing the query.
3. **Memory Display**: If the memory used is greater than 1 GB, it will show in GB. If it’s less than 1 GB, it will show in MB.

```php
// Get initial memory usage
$initialMemory = memory_get_usage();

// Execute the query and store the result
$datahotel = DB::select(DB::raw('
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT cityLongName AS name, NULL AS address FROM hotels_b2b_city)
    UNION
    (SELECT name, address FROM hotels_b2b
    JOIN hotels_b2b_additional_info ON hotels_b2b.HotelID = hotels_b2b_additional_info.hotelid
    WHERE hotels_b2b_additional_info.selectStatus != 2
    AND hotels_b2b_additional_info.selectStatus != "")
    UNION
    (SELECT cityName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT stateCode AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT destinationName AS name, NULL AS address FROM hotels_b2b)
    UNION
    (SELECT name, NULL AS address FROM cities WHERE deleted_at IS NULL)
    UNION
    (SELECT name, NULL AS address FROM states WHERE deleted_at IS NULL)
    UNION
    (SELECT name, address FROM hotels WHERE deleted_at IS NULL)
'));

// Get memory usage after storing the result
$finalMemory = memory_get_usage();
$memoryUsed = $finalMemory - $initialMemory;

// Convert to MB or GB
if ($memoryUsed >= 1024 * 1024 * 1024) {
    $memoryUsed = number_format($memoryUsed / (1024 * 1024 * 1024), 2) . ' GB';
} else {
    $memoryUsed = number_format($memoryUsed / (1024 * 1024), 2) . ' MB';
}

// Output the memory used
echo "Memory used: " . $memoryUsed;

// or 
dd("Memory used: " . $memoryUsed);

```
 