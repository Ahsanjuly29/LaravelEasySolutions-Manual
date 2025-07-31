 

# 📊 Laravel - Memory & Time Usage

## 🔍 Check Peak Memory Usage

Use this to log the highest memory used during the request:

```php
Log::info('Memory: ' . memory_get_peak_usage(true));
```

---

## ⏱️ Measure Processing Time

### ✅ Method 1: Using `App::finish()`

Logs how long the full request takes:

```php
$timeStart = microtime(true);

App::finish(function() use ($timeStart) {
    $diff = microtime(true) - $timeStart;
    $sec = intval($diff);
    $micro = $diff - $sec;

    Log::debug(Request::getMethod() . "[" . Request::url() . "] Time: " . round($micro * 1000, 4) . " ms");
});
```

---

### ✅ Method 2: Around a specific block (e.g. DB query)

Measure time for a specific code block:

```php
$start = microtime(true);

// your code here...

$time = microtime(true) - $start;
```

---

### ✅ Method 3: From Laravel start

Quick way to check how much time passed since app started:

```php
var_dump(microtime(true) - LARAVEL_START);
```

---

Keep it light, log only when needed ✅

---
 