# ✅ Stateless Sanctum API with Blade Frontend

## ✅ Quick Recap

| Feature         | Web (Blade)                  | API (Stateless)                          |
| --------------- | ---------------------------- | ---------------------------------------- |
| Authentication  | Session + Cookies (stateful) | API Token (Bearer Token)                 |
| Token storage   | N/A                          | Stored client-side (localStorage, etc.)  |
| Request headers | Cookies sent automatically   | `Authorization: Bearer {token}` required |
| CSRF Protection | Yes                          | Not applicable (token based)             |

---

## 🔐 Security Notes

* Always use HTTPS to protect tokens in transit.
* Protect API tokens — never expose them in public.
* Revoke tokens on logout or suspicious activity.
* Use appropriate token expiration policies.
* Use Laravel’s built-in CSRF protection for web forms.

---

## 🧪 Testing

1. Register or login via Blade UI web forms.
2. Retrieve token from API login response when testing API login.
3. Use tools like Postman or your frontend to send authenticated requests with the token.
4. Validate proper JSON error responses on API failures.
5. Confirm session cookies (`laravel_session`, `XSRF-TOKEN`) for web authentication.

---

> **Hybrid Laravel 12 app using Sanctum where:**
>
> * **Web** uses Sanctum session + cookies (stateful)
> * **API** uses Sanctum **Personal Access Tokens** (stateless, no cookies)

**Meaning:**

* API authentication is token-based, not cookie-based
* Clients send `Authorization: Bearer {token}` on each API request

---

## 🚀 Overview

This setup leverages Laravel Sanctum to provide:

* Seamless session-based authentication for Blade views (web)
* Secure, stateless API authentication using API tokens for SPAs, mobile apps, or external clients
* Clean separation of concerns and easy token management

---

## 🧱 Setup & Configuration

### 1. Install Laravel Breeze (Blade UI)

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run build
```

---

### 2. Install Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan install:api
php artisan migrate
```

choose 
*** Blade with Alpine  ***

## if want only API service then choose api only.

*** then testing choose phpunit or pest ***


---

### 3. Sanctum Config Update (`config/sanctum.php`)

```php
'stateful' => [],  // Empty disables cookie-based stateful API authentication
```

---

### 4. Token Generation on Login

In your login controller (`AuthenticatedSessionController`):

```php
public function store(LoginRequest $request)
{
    $request->authenticate();

    $request->session()->regenerate();

    $token = $request->user()->createToken('web-login-token')->plainTextToken;

    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'user' => $request->user(),
        ]);
    }

    return response()->noContent();
}
```

---

### 5. Logout (Revoke Token & Session)

```php
public function destroy(Request $request): Response
{
    $request->user()?->currentAccessToken()?->delete(); // Revoke token

    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return response()->noContent();
}
```

---

### 6. Exception Handling Customization (`bootstrap/app.php`)

```php
    use App\Http\Middleware\EnsureEmailIsVerified;
    use Illuminate\Cookie\Middleware\EncryptCookies;
    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
    use Illuminate\Routing\Middleware\SubstituteBindings;
    use Illuminate\Routing\Middleware\ThrottleRequests;
    use Illuminate\Session\Middleware\StartSession;
    use Illuminate\View\Middleware\ShareErrorsFromSession;
```

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Web middleware group (for Blade)
        $middleware->group('web', [
            EncryptCookies::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // API middleware group (stateless)
        $middleware->group('api', [
            ThrottleRequests::class . ':api',  // rate limiting
            SubstituteBindings::class,
        ]);

        // Middleware alias (if needed)
        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
        ]);
    })
```

### 7. Protect API Routes (`routes/api.php`)

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

### 8. API Client Usage

Send the token with each API request via HTTP header:

```http
Authorization: Bearer {personal-access-token}
```

---
 