# ✅ Stateless Sanctum API with Blade Frontend


> ✅ **Hybrid Laravel 12 app using Sanctum where:**

* WEB is authenticated via **Sanctum**,
* API is authenticated via **API tokens (Personal Access Tokens)Sanctum**, not cookies

This means:

* No more relying on session cookies for API
* You manually send `Authorization: Bearer {token}` in each AJAX request

---

# Laravel 12 + Sanctum (Stateless) + Blade + API CRUD

````markdown

## 1. Install & Setup

---

### Authentication Setup

#### 1. **Install Laravel Breeze (Blade UI)**

→ Adds Breeze package for simple auth scaffolding (login, register, etc.).

```bash
composer require laravel/breeze --dev
```

→ Installs Breeze using Blade templates (HTML-based Laravel views).

```bash
php artisan breeze:install blade
```

→ Installs frontend dependencies and compiles CSS/JS assets.

```bash
npm install && npm run build
```

---

#### 2. **Install Laravel Sanctum (API Authentication)**

```bash
composer require laravel/sanctum
```

→ Installs Sanctum for API token authentication.

```bash
php artisan sanctum:install
```

→ Publishes Sanctum config and migration files.

```bash
php artisan migrate
```

→ Adds Sanctum's tables to the database (for storing tokens).

---

## 3. Generate & Store Personal Access Token

### Example (in Login Controller on successful login):

```php
// Generate a personal access token for the authenticated user
$token = $user->createToken('web-token')->plainTextToken;

// You can store this token in cookies, cache, or return it in a response
```

### Return token in API response (example):

```php
return response()->json(['token' => $token]);
```

> 💡 **Tip:** You can store, update, or delete the token in cache or cookies depending on your use case (e.g., SPA or mobile app).
> 👉 [Click here to learn more about Laravel Cache and Cookies](https://github.com/Ahsanjuly29/LaravelEasySolutions-Manual/tree/main/Cache-Cookies)


---
