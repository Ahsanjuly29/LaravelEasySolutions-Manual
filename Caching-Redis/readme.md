
---

# Using Redis as Default Cache Driver in Laravel

This guide explains how to configure **Redis** (using **Predis**) as the default cache driver in a Laravel application,
including usage examples.

---

## 📦 1. Install Predis

If you're not using Laravel Sail or want to manually set up Predis, install the package via Composer:

```bash
composer require predis/predis
```

> **Note:** In newer Laravel versions with **Laravel Sail**, Predis is already installed.

---

## ⚙️ 2. Configure Redis in Laravel

### Update `config/database.php`

Replace or modify the `redis` configuration array:

```php
'redis' => [

'client' => env('REDIS_CLIENT', 'predis'),

'options' => [
'cluster' => env('REDIS_CLUSTER', 'redis'),
'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
],

'default' => [
'url' => env('REDIS_URL'),
'host' => env('REDIS_HOST', '127.0.0.1'),
'password' => env('REDIS_PASSWORD', null),
'port' => env('REDIS_PORT', '6379'),
'database' => env('REDIS_DB', '0'),
],

],
```

### Update `.env`

Add or modify the following line in your `.env` file:

```env
REDIS_CLIENT=predis
```

---

## 🧠 3. Storing & Updating Cache with Redis

Example: Save all city-related data to Redis using `Illuminate\Support\Facades\Redis`.

### In `StoreCacheController.php`

```php
use App\Models\CityList;
use Illuminate\Support\Facades\Redis;

public function updateCityListCache()
{
$cachedData = Redis::get('CityList');

if (isset($cachedData)) {
Redis::del('CityList'); // Delete old data
}

$cities = json_encode(CityList::get()->toArray());
Redis::set('CityList', $cities); // Store new data

return response()->json([
'status' => 1,
'message' => 'Fetched from database',
'data' => $cities,
]);
}
```

---

## 🚀 4. Using Laravel Cache Facade with Redis

If you're using **Laravel Sail**, update these configurations:

### `.env`

```env
CACHE_DRIVER=redis
```

### In `config/cache.php`

Make sure the driver is set to `redis`.

### Controller Example

```php
use Illuminate\Support\Facades\Cache;

public function getCityListCacheData()
{
$Citylists = Cache::get('CityList');

if (empty($Citylists)) {
if ($this->updateCityListCache()) {
$Citylists = Cache::get('CityList');
}
}

return $Citylists;
}
```

---

## ✅ Summary

* Install `predis/predis` if not using Laravel Sail.
* Update `database.php` and `.env` to use `predis`.
* Store and fetch data from Redis using `Redis` or `Cache` facades.
* Set `CACHE_DRIVER=redis` to use Laravel's cache system with Redis.

---
---

 