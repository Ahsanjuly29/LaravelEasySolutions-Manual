
# 🔥 Firebase EasySolution for Laravel 12

> Complete Setup: Firebase Realtime DB, Auth (Email/Password), FCM Push Notifications, and Laravel Auth Integration (Session or Sanctum)

---

## ✅ Requirements

- Laravel 12+
- Firebase Project (https://console.firebase.google.com)
- Firebase Admin SDK JSON file
- PHP 8.1+

---

## 📦 1. Install Firebase PHP SDK

```bash
composer require kreait/firebase-php
```

---

## 🔐 2. Firebase Admin SDK Setup

1. Go to [https://console.firebase.google.com](https://console.firebase.google.com)
2. Project → Settings → **Service Accounts**
3. Click **Generate new private key**
4. Save it to:

```bash
storage/app/firebase/firebase_credentials.json
```

> 🚫 Do not commit this file to Git!

---

## 🛠️ 3. Create Firebase Service

**File:** `app/Services/FirebaseService.php`

```php
<?php
namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory())
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
    }

    public function auth()       { return $this->firebase->createAuth(); }
    public function database()   { return $this->firebase->createDatabase(); }
    public function firestore()  { return $this->firebase->createFirestore()->database(); }
    public function messaging()  { return $this->firebase->createMessaging(); }
}
```

---

## 🔑 4. Enable Firebase Email/Password Auth

* Go to **Firebase Console → Authentication → Sign-in method**
* Enable **Email/Password**

---

## 🧑‍💻 5. Add Controller for Login

**File:** `app/Http/Controllers/FirebaseAuthController.php`

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseService;
use App\Models\User;

class FirebaseAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $firebase = new FirebaseService();
            $auth = $firebase->auth();

            $signInResult = $auth->signInWithEmailAndPassword(
                $request->email,
                $request->password
            );

            $uid = $signInResult->firebaseUserId();
            $userRecord = $auth->getUser($uid);

            $user = User::updateOrCreate(
                ['firebase_uid' => $uid],
                [
                    'name'  => $userRecord->displayName ?? $request->email,
                    'email' => $request->email,
                ]
            );

            Auth::login($user);

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
```

---

## ✏️ 6. Update User Model

**File:** `app/Models/User.php`

```php
protected $fillable = ['name', 'email', 'firebase_uid'];
```

---

## 🧱 7. Add `firebase_uid` Column

Create migration:

```bash
php artisan make:migration add_firebase_uid_to_users_table
```

Edit the migration:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('firebase_uid')->nullable()->unique();
});
```

Run:

```bash
php artisan migrate
```

---

## 🔗 8. Define Login Route

**File:** `routes/api.php`

```php
use App\Http\Controllers\FirebaseAuthController;

Route::post('/firebase-login', [FirebaseAuthController::class, 'login']);
```

---

## 💬 9. Firebase Realtime Database Example

**Routes in:** `routes/web.php`

```php
use App\Services\FirebaseService;

Route::get('/firebase-write', function () {
    $firebase = new FirebaseService();
    $db = $firebase->database();
    $db->getReference('users/001')->set([
        'name'  => 'Alice',
        'email' => 'alice@example.com'
    ]);
    return 'Data written!';
});

Route::get('/firebase-read', function () {
    $firebase = new FirebaseService();
    $db = $firebase->database();
    $user = $db->getReference('users/001')->getValue();
    return $user;
});
```

---

## 📲 10. Sending Push Notifications

Also in `routes/web.php`:

```php
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

Route::get('/send-notification', function () {
    $firebase = new FirebaseService();
    $messaging = $firebase->messaging();

    $message = CloudMessage::withTarget('token', 'YOUR_DEVICE_TOKEN')
        ->withNotification(Notification::create('Hello!', 'Notification from Laravel 12'));

    $messaging->send($message);

    return 'Notification sent!';
});
```

---

## ✅ 11. (Optional) Sanctum for Token-Based Auth

Install and configure:

```bash
composer require laravel/sanctum
php artisan vendor:publish --tag=sanctum-config
php artisan migrate
```

Then in login controller, after `Auth::login($user);`, add:

```php
$token = $user->createToken('auth_token')->plainTextToken;

return response()->json([
    'message' => 'Login successful',
    'token'   => $token,
    'user'    => $user,
]);
```

Use `auth:sanctum` middleware to protect routes.

---

## ✅ Feature Summary

* Firebase Realtime Database ✅
* Firebase Email/Password Auth ✅
* Laravel Auth from Firebase login ✅
* Sync Firebase users into Laravel DB ✅
* FCM Push Notification ✅
* Support for API token auth with Laravel Sanctum ✅

 