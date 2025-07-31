# 🛡️ Unique Validation in Laravel (Request-Based)

This guide explains how to **validate unique data** (like an email) in Laravel when using **Form Requests** — especially when you want the uniqueness to depend on more than one column.

## 🧪 Scenario

You have a table called `agency_customers`, and you want to make sure that:

* The **email** is unique **per agency** (`agency_id`).
* The validation works for both **Create** and **Update** requests.

---

## ✅ While Creating

Use this rule inside your `FormRequest`:

```php
'email' => [
    'nullable',
    'string',
    'max:249',
    Rule::unique('agency_customers')->where(function ($query) use($email, $agencyId) {
        return $query->where([
            'email' => $email,
            'agency_id' => $agencyId
        ]);
    }),
],
```

### ✔️ What it does:

Checks if there's **any other customer** with the **same email and agency ID**. If yes, it will **fail the validation**.

---

## 🔁 While Updating

Use this rule:

```php
'email' => [
    'nullable',
    'string',
    'max:249',
    Rule::unique('agency_customers')->ignore($this->id)->where(function ($query) use($email, $agencyId) {
        return $query->where([
            'email' => $email,
            'agency_id' => $agencyId
        ]);
    }),
],
```

### ✔️ What it does:

* Ignores the current record (`$this->id`) — so it doesn’t validate against itself.
* Still ensures **no other record** has the same **email + agency\_id**.

---

## 📦 Requirements

Make sure you import this at the top of your Form Request:

```php
use Illuminate\Validation\Rule;
```

---

## 👀 Notes

* `nullable`: allows the field to be empty.
* Use `use($email, $agencyId)` to pass the current values into the query.
* This is very useful for **multi-tenant applications** where uniqueness is scoped per user, company, or agency.

---
