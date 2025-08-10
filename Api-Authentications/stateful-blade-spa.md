## 🧱 Setup from Scratch

### 1. ✅ Install Laravel

```bash
laravel new air
cd air
```

or

```bash
composer create-project laravel/laravel air
cd air
```

---

### 2. ✅ Sanctum Setup

```bash
composer require laravel/sanctum
php artisan vendor:publish --tag=sanctum-config
php artisan migrate
```

---

### 3. ✅ Install Breeze

Choose Blade or React:

#### Blade:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
php artisan migrate
```

#### OR React:

```bash
composer require laravel/breeze --dev
php artisan breeze:install react
npm install && npm run dev
php artisan migrate
```

---

### 4. ✅ `.env` Configuration

Update `.env` with these values:

```env
APP_URL=http://air.test

SESSION_DRIVER=database
SESSION_DOMAIN=air.test
SANCTUM_STATEFUL_DOMAINS=air.test

SESSION_SECURE_COOKIE=false  # true if using HTTPS
SESSION_SAME_SITE=lax
```

> If using `SESSION_DRIVER=database`, run:

```bash
php artisan session:table
php artisan migrate
```

---

### 5. ✅ `bootstrap/app.php` (Laravel 12 Middleware Setup)

Update middleware groups like this:

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
    ->create();
```

---

### 6. ✅ API Routes (`routes/api.php`)

Protect API routes with `auth:sanctum` middleware:

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

### 7. ✅ If Using Axios (React)

Before login, call CSRF cookie endpoint:

```js
await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
await axios.post('/login', { email, password }, { withCredentials: true });
```

Also set defaults:

```js
axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://air.test';
```

---

### 8. ✅ Final Cleanup

Run these commands to clear config and cache, then restart Valet or your local server:

```bash
php artisan config:clear
php artisan cache:clear
valet restart
```

---

## 🧪 Test

1. Open `http://air.test`
2. Register or login with Breeze form
3. Check browser devtools for `XSRF-TOKEN` and `laravel_session` cookies
4. Access `/api/user` via browser or Postman (cookies sent automatically)

---

 