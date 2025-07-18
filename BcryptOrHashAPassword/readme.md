
---

## 🔐 How Password Encrypt (Hash) in Laravel Authentication

### 💡 How to Set Password (Hashing) in Laravel Seeder?

---

> ✅ Use the following syntax to hash a password in Laravel:

```php
Hash::make('your string / password')
```

> 📌 **The result will be like this:**

```
$2y$10$aOTEIPei1UK7ACXjlShg7eqrv/l1HIrfW/1gaVHk5147seAp901w2
```

---

### 📎 Notes

* 🔒 No need to use quotes (`''`) for numbers.

> 💡 **Example:**

```php
Hash::make(12345678)
```
 