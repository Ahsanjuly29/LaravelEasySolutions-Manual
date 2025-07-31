
---

# 🚀 Firebase EasySolution for Laravel 11.2

---

## ✅ 1. Requirements

* Laravel 12
* Firebase project
* Firebase Admin SDK JSON key

---

## 📦 2. Install Firebase PHP SDK

Run this in your Laravel root directory:

```bash
composer require kreait/firebase-php
```

---

## 🔑 3. Add Firebase Admin SDK JSON Key

### Get JSON Key:

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project
3. Navigate to:
   **Project Settings → Service Accounts → Generate new private key**

### Save the file:

Put it in:
`storage/app/firebase/firebase_credentials.json`

> **✅ Best Practice**: Never commit this file to Git!

---

## 🛠️ 4. Create Firebase Service in Laravel 11.2

### File: `app/Services/FirebaseService.php`

```php
<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
    }

    public function database()
    {
        return $this->firebase->createDatabase();
    }

    public function firestore()
    {
        return $this->firebase->createFirestore()->database();
    }

    public function auth()
    {
        return $this->firebase->createAuth();
    }

    public function messaging()
    {
        return $this->firebase->createMessaging();
    }
}
```

---

## 📥 5. Bind FirebaseService (Optional but clean)

### File: `bootstrap/app.php` or use Laravel’s service container in a ServiceProvider:

```php
app()->bind('firebase', function () {
    return new \App\Services\FirebaseService();
});
```

Now you can use:

```php
$firebase = app('firebase');
```

---

## 🧪 6. Example Routes Using Firebase in Laravel 11.2

### File: `routes/web.php`

```php
use Illuminate\Support\Facades\Route;
use App\Services\FirebaseService;

Route::get('/firebase-write', function () {
    $firebase = new FirebaseService();
    $db = $firebase->database();

    $db->getReference('users/001')->set([
        'name' => 'Alice',
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

## 🧾 7. Push Notification Example (FCM)

```php
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

Route::get('/send-notification', function () {
    $firebase = new FirebaseService();
    $messaging = $firebase->messaging();

    $message = CloudMessage::withTarget('token', 'DEVICE_FCM_TOKEN')
        ->withNotification(Notification::create('Hello!', 'This is from Laravel 11.2'));

    $messaging->send($message);

    return 'Notification sent!';
});
```

---

## 🔐 8. Firebase Authentication Token Check

```php
Route::get('/verify-token', function () {
    $firebase = new FirebaseService();
    $auth = $firebase->auth();

    $idToken = request()->bearerToken();

    try {
        $verified = $auth->verifyIdToken($idToken);
        return response()->json(['uid' => $verified->claims()->get('sub')]);
    } catch (\Throwable $e) {
        return response()->json(['error' => 'Invalid token'], 401);
    }
});
```

---

## 📂 Suggested File Structure (Laravel 11.2)

```
app/
├── Services/
│   └── FirebaseService.php
routes/
├── web.php
storage/
└── app/
    └── firebase/
        └── firebase_credentials.json
```

---

## 🎉 You’re Done!

✅ Firebase now works with your Laravel 11.2 app!
You can:

* Write/read from Firebase Realtime Database
* Use Firebase Auth
* Send Push Notifications via FCM
* (Optional) Use Firestore and Cloud Storage with the same setup

---

Let me know if you want:

* 📦 Package this into a Laravel Facade
* 🧪 Unit test Firebase features
* 🔒 Use `.env` to store the credentials path securely

=

