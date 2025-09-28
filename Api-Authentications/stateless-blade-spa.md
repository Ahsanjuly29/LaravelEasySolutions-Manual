# ✅ Stateless Sanctum API with Blade Frontend

> **Hybrid Laravel 12 app using Sanctum where:**
>
> * **Web** is authenticated via Sanctum (session + cookies)
> * **API** is authenticated via Sanctum **API tokens (Personal Access Tokens)**, *not* cookies

**This means:**

* No reliance on session cookies for API authentication
* API clients manually send `Authorization: Bearer {token}` with each request

---

## Laravel 12 + Sanctum (Stateless) + Blade + API CRUD Setup

---

### 1. Install & Setup

#### Install Laravel Breeze (Blade UI)

Adds Breeze for basic auth scaffolding (login, registration):

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

---

#### Install Laravel Sanctum (API Token Authentication)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan sanctum:install
php artisan migrate
```

---

### 2. Sanctum Configuration

Edit `config/sanctum.php`:

```php
'stateful' => [], // Empty array disables cookie-based stateful auth for APIs
```

---

### 3. Token Generation on Login (Stateless API Support)

In your `AuthenticatedSessionController` or login controller:

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

    return response()->noContent(); // For normal web login flow
}
```

---

### 4. Logout (Revoke Token & Session)

```php
public function destroy(Request $request): Response
{
    $request->user()?->currentAccessToken()?->delete(); // Revoke current token

    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return response()->noContent();
}
```

---

### 5. Exception Handling Customization

Modify your `bootstrap/app.php` to customize exception rendering for APIs:

```php
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);

    $middleware->alias([
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    ]);
})
->withExceptions(function ($exceptions) {
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

        // Default handling: let Laravel handle it
        return null;
    });
})
->create();
```

---

### 6. Protect API Routes

In `routes/api.php`:

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

### 7. API Client Requirement

When consuming API routes, clients must send the personal access token in the Authorization header:

```http
Authorization: Bearer {personal-access-token}
```

---

### 💡 Tips

* You can manage tokens (store, update, delete) via cache or cookies depending on your client type (SPA, mobile app, etc.).
* For more info on Cache and Cookies in Laravel, check [Laravel Cache & Cookies Guide](https://github.com/Ahsanjuly29/LaravelEasySolutions-Manual/tree/main/Cache-Cookies)

---
 