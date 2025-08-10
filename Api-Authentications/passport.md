

# ✅ Stateless Passport API with Blade Frontend

> ✅ **Hybrid Laravel 12 app using Passport where:**

* Web uses Laravel Blade views
* API is secured with **OAuth2 tokens via Laravel Passport**
* No reliance on sessions or cookies for API requests

This means:

* Authentication is fully **stateless**
* You must manually send `Authorization: Bearer {access_token}` in each API request

---

# Laravel 12 + Passport (Stateless) + Blade + API CRUD

````markdown

## 1. Install & Setup

---

### Authentication Setup

#### 1. **Install Laravel Passport**

```bash
composer require laravel/passport
```

→ Installs Passport package for OAuth2 authentication.

---

#### 2. **Run Migrations & Install Passport**

```bash
php artisan migrate
php artisan passport:install
```

→ Creates necessary Passport tables and generates encryption keys.

---

#### 3. **Configure AuthServiceProvider**

In `app/Providers/AuthServiceProvider.php`:

```php
use Laravel\Passport\Passport;

public function boot()
{
    Passport::routes();
}
```

---

#### 4. **Update `config/auth.php`**

Set Passport as the API driver:

```php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

---

#### 5. **Add HasApiTokens to User Model**

In `App\Models\User.php`:

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    // ...
}
```

---

## 2. Issue Access Token (Login)

### Example (in Login Controller):

```php
use Illuminate\Support\Facades\Auth;

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();

    $token = $user->createToken('api-token')->accessToken;

    return response()->json(['token' => $token]);
}
```

> 🛡️ This token must be sent in the header for all protected API calls:  
> `Authorization: Bearer {access_token}`

---

## 3. Protecting API Routes

In `routes/api.php`, apply the `auth:api` middleware:

```php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

You can now build your protected API CRUD using this middleware.

---

## 💡 Tips

- Store tokens securely (e.g., in frontend local storage or HTTP-only cookies)
- Use Postman or Axios for testing token-based headers
- You can revoke tokens, use scopes, and manage expiry with Passport's full OAuth2 features

---

📘 Want to manage tokens using **cache or cookies**?  
👉 [Click here for cache/cookie helper guide](https://github.com/Ahsanjuly29/LaravelEasySolutions-Manual/tree/main/Cache-Cookies)

---
 