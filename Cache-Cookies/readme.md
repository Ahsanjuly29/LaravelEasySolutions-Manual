
# ⚙️ Laravel Cache & Cookie Management

This project uses **Laravel helper functions** to manage **cache** and **cookies** efficiently — especially for handling **personal access tokens** with Laravel Sanctum.

---

## 📦 Basic Usage Examples

### ✅ Cache

```php
// Store in cache for 1 hour (60 minutes)
cache()->put('key_name', 'value_here', 60);

// Read from cache
$value = cache()->get('key_name');

// Update (same as put)
cache()->put('key_name', 'new_value', 60);

// Delete from cache
cache()->forget('key_name');
```

---

### 🍪 Cookies

```php
// Store in cookie (with response)
return response('OK')->cookie('cookie_name', 'value_here', 60); // 60 minutes

// Read from cookie
$value = request()->cookie('cookie_name');

// Update (overwrite)
return response('Updated')->cookie('cookie_name', 'new_value', 60);

// Delete cookie
return response('Deleted')->withoutCookie('cookie_name');
```

---

## 🛠️ Helper Functions

Custom helper functions for managing user tokens via cache:

```php
if (!function_exists('refreshTokenCache')) {
    function refreshTokenCache()
    {
        if (auth()->check()) {
            $user = auth()->user();

            deleteUserToken(); // Delete existing tokens

            $token = $user->createToken('api-token')->plainTextToken;

            cache()->put('user_token_' . $user->id, $token, 60 * 60 * 24); // Cache for 24 hours

            return $token;
        }

        return null;
    }
}

if (!function_exists('getUserToken')) {
    function getUserToken()
    {
        if (auth()->check()) {
            $token = auth()->user()->tokens()->where('name', 'api-token')->first();
            if ($token) {
                return cache()->get('user_token_' . auth()->id()) ?? null;
            }
        }

        return null;
    }
}

if (!function_exists('deleteUserToken')) {
    function deleteUserToken()
    {
        if (auth()->check()) {
            $user = auth()->user();

            $user->tokens()->where('name', 'api-token')->delete(); // Delete from DB
            cache()->forget('user_token_' . $user->id); // Delete from cache

            return 'done';
        }

        return null;
    }
}
```

---

## 🧩 Using Helper Functions (Token Example)

### 🔁 `refreshTokenCache()`

* Deletes existing tokens
* Creates a new **Sanctum token**
* Stores it in **cache** for 24 hours
* Returns the new token

```php
$token = refreshTokenCache(); // Key: user_token_{user_id}
```

---

### 📥 `getUserToken()`

* Checks if the token exists for the logged-in user
* Retrieves it from **cache**

```php
$token = getUserToken();
```

---

### 🗑️ `deleteUserToken()`

* Deletes the token from both the **database** and **cache**

```php
deleteUserToken();
```

---
 