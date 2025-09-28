
# Laravel 12 + Sanctum (Stateful) + Blade + AJAX — README

## ✅ Quick Recap

| Step | Task                                                     |
| ---- | -------------------------------------------------------- |
| 1    | Install Laravel                                          |
| 2    | Install Sanctum                                          |
| 3    | Install Breeze (Blade)                                   |
| 4    | Set up `.env` for domain, sessions                       |
| 5    | Add middleware in `bootstrap/app.php`                    |
| 6    | Create `routes/api.php`                                  |
| 7    | Add jQuery CDN                                           |
| 8    | Login with Blade form (no AJAX login)                    |
| 9    | Call `/sanctum/csrf-cookie`, then make AJAX API requests |
| 10   | Use logout form or controller                            |

---

## 🧪 Testing

1. Visit `http://yourapp.test`
2. Register or login using the Blade form
3. Open browser DevTools and confirm `laravel_session` and `XSRF-TOKEN` cookies are set
4. Call your API using jQuery as shown above
5. You should see authenticated user info

---

## 🔐 Security Notes

* Sanctum uses session + CSRF protection for browser-based apps
* Cookies and tokens are **automatically handled**
* Do NOT use personal access tokens unless you're building a **stateless API**

---

---

## Overview

This setup uses **Laravel Sanctum** for **stateful authentication** in a hybrid app where:

* User logs in using **regular Blade forms** (no AJAX login).
* The session cookie and CSRF token handle API authentication automatically.
* AJAX requests send cookies and CSRF tokens to authenticate against protected API routes.
* No manual token management on frontend.
* Uses jQuery AJAX with cookies and JSON content type.

---

## Step-by-Step Setup

### 1. Install Laravel

```bash
composer create-project laravel/laravel yourapp
cd yourapp
```

---

### 2. Install Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --tag=sanctum-config
php artisan migrate
```

---

### 3. Install Breeze (Blade scaffolding)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
php artisan migrate
```

---

### 4. Configure `.env`

```env
APP_URL=http://yourapp.test

SESSION_DRIVER=database
SESSION_DOMAIN=yourapp.test
SANCTUM_STATEFUL_DOMAINS=yourapp.test

SESSION_SECURE_COOKIE=false  # true if HTTPS
SESSION_SAME_SITE=lax
```

> If using `SESSION_DRIVER=database`, run:

```bash
php artisan session:table
php artisan migrate
```

---

### 5. Middleware Setup (`bootstrap/app.php`)

```php
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->group('web', [
            EncryptCookies::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
      // Render JSON for API or AJAX requests only
      $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
          return $request->is('api/*') || $request->expectsJson();
      });

      // Customize JSON error responses
      $exceptions->render(function (Throwable $e, Request $request) {
          if ($e instanceof AuthenticationException) {
              return response()->json([
                  'status' => 0,
                  'message' => 'Unauthenticated.',
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

          // Optionally handle validation exceptions (if needed)

          // Default: Let Laravel handle it (show normal HTML error page for web)
          return null;
      });
    })
    ->create();
```

---

### 6. Protect API routes (`routes/api.php`)

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

*** if missing then install api***
```bash
  php artisan install:api
```

---

### 7. Frontend jQuery AJAX Setup

Set jQuery AJAX defaults to send cookies and JSON content type globally:

```html
  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
      // Always send cookies (including session, CSRF)
      $.ajaxSetup({
          xhrFields: {
              withCredentials: true
          },
          headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
          }
      });
  </script>
```
### 8. ✅ Make Authenticated API Requests via AJAX (jQuery)

#### After logging in using the Blade form, Laravel will set cookies like:

* `laravel_session`
* `XSRF-TOKEN`

#### These will be automatically sent with AJAX requests **if** you:

* Use `withCredentials: true`
* Use the correct domain
* Call `/sanctum/csrf-cookie` once before any request (not before every request)

---

### 9. CSRF Cookie Request (must be done before login form submit)

Before submitting the login form (via regular HTML form POST), fetch the CSRF cookie:

```js
$.get('http://yourapp.test/sanctum/csrf-cookie')
  .done(function () {
    console.log('CSRF cookie set, now you can submit the login form.');
  });
```

---

### ✅ Example: jQuery API Flow (After Login)

```html
<script>
    // Step 1: Set up jQuery (already done above)

    // Step 2: Call /sanctum/csrf-cookie once
    $.get('http://yourapp.test/sanctum/csrf-cookie')
        .done(function () {
            console.log('CSRF cookie set');

            // Step 3: Call protected API route
            $.get('http://yourapp.test/api/user')
                .done(function (data) {
                    console.log('Authenticated user data:', data);
                })
                .fail(function (xhr) {
                    console.error('Unauthorized or error:', xhr.responseJSON);
                });
        });
</script>
```

✅ You only need to call `/sanctum/csrf-cookie` **once** after page load. Not before every request.

---

### 10. ✅ Logout (Blade or AJAX)

You can log out using the default Blade logout form or programmatically:

```html
<form method="POST" action="/logout">
    @csrf
    <button type="submit">Logout</button>
</form>
```

Or in controller:

```php
public function destroy(Request $request): Response
{
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->noContent();
}
```

---