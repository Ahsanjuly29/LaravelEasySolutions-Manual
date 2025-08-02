
---

## 🚀 Setup: Laravel 12 + Sanctum + Breeze (React) – Stateful SPA Auth

### 1. **Create Laravel 12 Project(if not exist)**

```bash
composer create-project laravel/laravel example-app
cd example-app
```

### 2. **Install Sanctum**

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 3. **Install Breeze (React UI)**

```bash
composer require laravel/breeze --dev
php artisan breeze:install react
npm install && npm run dev
php artisan migrate
```

This gives built-in login/register UI via React.

### 4. **Configure `.env`**

```env
SESSION_DRIVER=cookie
SESSION_DOMAIN=.example.com
SANCTUM_STATEFUL_DOMAINS=example.com
```

### 5. **Update Sanctum Config (`config/sanctum.php`)**

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'example.com')),
'expiration' => null,
```

### 6. **Register Middleware in Bootstrap**

Laravel 12 uses `bootstrap/app.php` with middleware groups — no need to edit `Kernel.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
});
```
install api if not exist

```bash
php artisan install:api
```

This ensures SPA requests (same-domain) use session cookies.

### 7. **Define Protected API Routes (`routes/api.php`)**

```php
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', fn(Request $r) => $r->user());
  Route::get('/users', fn() => \App\Models\User::all());
});
```

### 8. **Serve Your App**

```bash
php artisan serve
```
## Done(SPA Stateful setup authentication)


    ---
    ---
    ---


change this all later 
## ✅ Session-Based Authentication (Email/Password)

### Setup (Laravel Breeze)

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate
```

* Provides: Login, registration, email verification, password reset.
* Blade-based frontend, session + CSRF-secured.

🔗 [Laravel Breeze Docs](https://laravel.com/docs/10.x/starter-kits#laravel-breeze)

---

## 🔄 Login Using Email or Username

### Update LoginRequest

```php
// app/Http/Requests/Auth/LoginRequest.php

public function authenticate(): void
{
    $login = $this->input('login');
    $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    if (!Auth::attempt([$field => $login, 'password' => $this->password])) {
        throw ValidationException::withMessages(['login' => __('auth.failed')]);
    }

    $this->session()->regenerate();
}
```

### Update Blade Form

```blade
<input type="text" name="login" placeholder="Email or Username" required>
<input type="password" name="password" placeholder="Password" required>
```

---

## 📱 Login Using Mobile Number + OTP (Passwordless)

### 1. Add Mobile Column to Users Table

```bash
php artisan make:migration add_mobile_to_users_table --table=users
```

Edit migration:

```php
$table->string('mobile')->unique()->nullable();
```

```bash
php artisan migrate
```

---

### 2. Create OTP Model & Table

```bash
php artisan make:model Otp -m
```

Edit migration:

```php
$table->id();
$table->string('mobile');
$table->string('code');
$table->boolean('used')->default(false);
$table->timestamps();
```

```bash
php artisan migrate
```

---

### 3. OTP Controller (`OtpAuthController`)

```php
public function sendOtp(Request $request)
{
    $request->validate(['mobile' => 'required|digits:10']);

    $code = rand(100000, 999999);

    Otp::create([
        'mobile' => $request->mobile,
        'code' => $code,
        'used' => false,
        'created_at' => now(),
    ]);

    // TODO: Integrate SMS API here (e.g., Twilio)

    return response(['message' => 'OTP sent']);
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'mobile' => 'required|digits:10',
        'code' => 'required|digits:6',
    ]);

    $otp = Otp::where('mobile', $request->mobile)
              ->where('code', $request->code)
              ->where('used', false)
              ->where('created_at', '>=', now()->subMinutes(5)) // OTP valid for 5 mins
              ->first();

    if (!$otp) {
        return response(['error' => 'Invalid or expired OTP'], 401);
    }

    $user = User::firstOrCreate(['mobile' => $request->mobile]);

    Auth::login($user);

    $otp->update(['used' => true]);

    return response(['message' => 'Logged in']);
}
```

---

### 4. Routes

```php
Route::post('/otp/send', [OtpAuthController::class, 'sendOtp'])->middleware('throttle:5,1');
Route::post('/otp/verify', [OtpAuthController::class, 'verifyOtp'])->middleware('throttle:5,1');
```

---

## 🔐 Token-Based API Authentication (Laravel Sanctum)

### Setup

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### User Model

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens;
}
```

---

## 🔧 Sanctum API Guard (Token Auth)

### Login (Token Issuing)

```php
public function login(Request $request)
{
    $request->validate(['email' => 'required|email', 'password' => 'required']);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response(['token' => $token, 'user' => $user]);
}
```

### Logout

```php
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response(['message' => 'Logged out']);
}
```

---

## 🛡️ Protect Routes with Sanctum

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

## ⚙️ Use Token in API Requests

Add to request headers:

```
Authorization: Bearer your_token_here
```

---

## Optional: SPA Authentication with Sanctum

* SPA uses cookie-based auth with `SANCTUM_STATEFUL_DOMAINS` set in `.env`
* Frontend and backend must share the same top-level domain or properly configured domains.

🔗 [Sanctum SPA Auth Docs](https://laravel.com/docs/10.x/sanctum#spa-authentication)

---

## 🔐 JWT Authentication (Stateless API)

### Install Package

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

---

### User Model Setup

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
```

---

### Auth Controller (Login / Token)

```php
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!$token = auth()->attempt($credentials)) {
        return response(['error' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
}

protected function respondWithToken($token)
{
    return response([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60,
        'user' => auth()->user()
    ]);
}
```

---

### Protected Routes

```php
Route::middleware('auth:api')->get('/user', function () {
    return response()->json(auth()->user());
});
```

---

### Logout

```php
public function logout()
{
    auth()->logout();
    return response(['message' => 'Logged out']);
}
```

---

### Token Refresh

```php
public function refresh()
{
    return $this->respondWithToken(auth()->refresh());
}
```

---

### Set Guard in `config/auth.php`

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

## 🔐 OAuth2 Authentication (Laravel Passport)

### Install Passport

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

---

### User Model

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens;
}
```

---

### Auth Config (`config/auth.php`)

```php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

---

### Register Passport Routes

```php
// App\Providers\AuthServiceProvider.php
public function boot()
{
    $this->registerPolicies();

    Passport::routes();
}
```

---

### Issue Tokens

* Password Grant: Clients send email/password to `/oauth/token` to get tokens.
* Personal Access Tokens:

```php
$token = $user->createToken('token-name')->accessToken;
```

---

### Protect Routes

```php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

🔗 [Laravel Passport Docs](https://laravel.com/docs/10.x/passport)

---

## 🔗 Social Login (Laravel Socialite)

### Install Socialite

```bash
composer require laravel/socialite
```

---

### Setup in `config/services.php`

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URL'),
],
```

---

### Routes & Controller Example

```php
use Laravel\Socialite\Facades\Socialite;

Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/callback', function () {
    $user = Socialite::driver('google')->user();

    $finduser = User::where('email', $user->email)->first();

    if ($finduser) {
        Auth::login($finduser);
    } else {
        $newUser = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'google_id'=> $user->id,
        ]);

        Auth::login($newUser);
    }

    return redirect('/home');
});
```

🔗 [Laravel Socialite Docs](https://laravel.com/docs/10.x/socialite)

---

## 🔐 Two-Factor Authentication (2FA)

* Use Laravel Fortify or Jetstream for built-in TOTP 2FA support.
* Supports apps like Google Authenticator, Authy.
* Setup includes enabling 2FA features and routes.

🔗 [Laravel Fortify 2FA Docs](https://laravel.com/docs/10.x/fortify#two-factor-authentication)

---

## 👥 Role & Permission (Spatie Package)

### Install

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

---

### Usage

Add trait to User:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasRoles;
}
```

Assign roles & permissions:

```php
$user->assignRole('admin');
$user->givePermissionTo('edit articles');
```

Middleware to protect routes:

```php
Route::group(['middleware' => ['role:admin']], function () {
    // admin-only routes
});
```

🔗 [Spatie Laravel Permission Docs](https://spatie.be/docs/laravel-permission/v5/introduction)

---
 