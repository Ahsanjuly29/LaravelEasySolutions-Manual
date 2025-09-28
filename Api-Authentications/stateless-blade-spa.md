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
php artisan breeze:install blade
npm install && npm run build
```

---

### 2. Install Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan sanctum:install
php artisan migrate
```

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
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\SubstituteBindings;
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
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // API middleware group (stateless)
        $middleware->group('api', [
            ThrottleRequests::class . ':api',  // rate limiting
            SubstituteBindings::class,
        ]);

        // Middleware alias (if needed)
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Route not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Method not allowed.',
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            return null; // default Laravel error handling for other cases
        });
    })
    ->create();

```

---

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
 