
---

# 📚 CRUD Operations with AJAX and Yajra DataTables in Laravel

This guide shows how to implement **CRUD operations** in Laravel using **AJAX** for seamless interaction and **Yajra DataTables** for efficient data presentation, along with **Toastr** for alerts.

---

## ⚙️ Installation

### 1. Install Laravel

If Laravel is not installed, create a new project:

```bash
composer create-project --prefer-dist laravel/laravel myproject
```

---

## 🛠️ Setting Up the Environment

### 1. Create Model, Resource Controller, and Migration

Generate the model, controller, and migration with this command:

```bash
php artisan make:model ModelName -mcr --api
```

* `-m` : Create migration
* `-c` : Create controller
* `-r` : Resource controller (auto-generates CRUD methods)
* `--api` : API controller without view methods

---

### 2. Define Routes

#### 🌐 Web Routes (`routes/web.php`)

```php
Route::middleware('auth')->get('/ajax-crud', function () {
    return view('ajax.index', [
        'allData' => ModelName::orderBy('id', 'DESC')->paginate(10)
    ]);
});
```

#### 🔗 API Routes (`routes/api.php`)

```php
Route::middleware(['auth:sanctum'])->resource('api-model-name', ModelNameController::class);
```

> **Note:** In Laravel 12, the API routes folder might not exist by default. Create it by running:

```bash
php artisan install:api
```

---

# 📘 Model-Based Migration & Operations Guide

This guide describes how to create and manage migrations and models efficiently in a Laravel project. Follow these steps to define schema, model fillables, relationships, and scopes.

---

## 📂 Search Migration File & Add Columns

Locate the relevant migration file inside the `database/migrations` directory based on your model name. Add the required columns to the table.

```php
Schema::create('models', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->longText('description')->nullable();
    $table->string('status')->default('PENDING')->comment('PENDING', 'IN PROGRESS', 'COMPLETED');
    $table->unsignedBigInteger('assigned_to');
    $table->unsignedBigInteger('created_by');
    $table->dateTime('due_date');
    $table->timestamps();
});

Schema::table('models', function (Blueprint $table) {
    $table->foreign('assigned_to')->references('id')->on('users');
    $table->foreign('created_by')->references('id')->on('users');
});
```

> ✅ Define your schema and set up relationships via foreign keys after creating the table.

---

## 🧩 Model Operations

Navigate to the corresponding model inside `app/Models`.

### 🔐 Define Fillable Fields

Add fillable or guarded fields to control mass assignment.

```php
protected $fillable = [];
```

> 🛡️ Use either `$fillable` or `$guarded` based on your security preference.

---

### 🔗 Define Relationships

Create Eloquent relationships inside your model as needed.

```php
// public function relationshipCallerName()
// {
//     return $this->belongsTo(Model::class, 'foreign_key', 'primary_key');
// }
```

> 🧠 Update `relationshipCallerName`, `Model::class`, and keys as per your database design.

---

### 🔍 Add Local Scopes (Optional)

Use scopes to simplify commonly used query logic.

```php
// public function scopeOwner(Builder $query): void
// {
//     $query->with('assignedTo', 'createdBy')->whereAny(['assigned_to', 'created_by'], Auth::user()->id);
// }
```

> 🎯 Great for filtering data based on ownership or access logic.

---

## ⚙️ CRUD Operations

This section describes how to integrate front-end scripts and styles for a smooth **AJAX-based CRUD system** using Blade templates and jQuery.

---

### 🧱 Master Layout Setup (Client-Side)

Update your master Blade file (e.g., `resources/views/master/app.blade.php`) to include the necessary CSS and JS assets. These links support DataTables, Bootstrap, Font Awesome, Toastr, and AJAX logic.

#### 🖼️ Head Section

```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="bearer-token" content="{{ session('loginToken' . auth()->user()->id) }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">

    {{-- Css --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}" />

    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    {{-- Css Files  --}}
    {{-- @vite(['assets/css/app.css', 'assets/css/bootstrap.css', 'resources/sass/app.scss']) --}}

    @yield('custom-css')
</head>

@yield('main-body')
```

#### 📜 Scripts Section

```blade
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>

{{-- Js Files  --}}
{{-- @vite(['resources/assets/js/app.js', 'resources/assets/js/bootstrap.js', 'resources/assets/js/bootstrap.min.js', 'resources/assets/js/jquery.min.js']) --}}
@yield('custom-js')
@include('ajax.form-modal')
```

---

### ⚡ AJAX Initialization (Client-Side)

Use the Blade template below to set up AJAX response handling with success message support using Toastr.

```php
@extends('master.app')
@section('custom-css')
@endsection

@section('main-body')
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
            success = function(data) {
                if (data.message == 'Open Modal') {
                    openModal(data);
                } else {
                    toastr.success(data.message);
                    closeModal();
                }
            }
        });
    </script>
@endsection
```

---

## 🧭 Controller Method & Request Validation

This section outlines how to set up and organize your **controller**, optionally use **action classes**, and apply **form request validation** to build a clean and secure CRUD system in Laravel.

---

### 🗂️ Controller Setup

The main logic for your CRUD operations resides in the controller. You can use the default location or organize it for better structure.

#### ✅ Location:

You can find or move the controller to:

```
app/Http/Controllers/Api/ApiModelController.php
```

This controller was generated using the `-mcr` flag:

```bash
php artisan make:controller ApiModelController -mcr
```

After creation, it was moved into the `Api` folder. You can either:

* Keep the controller in the default `app/Http/Controllers` directory, **or**
* Move it into `app/Http/Controllers/Api` for better project structure.

> 🔁 If moved, update the **namespace** inside the controller:

```php
namespace App\Http\Controllers\Api;
```

Then update any route references accordingly.

---

### 🧱 CRUD Implementation

Inside your `ApiModelController`, you’ll have methods like:

* `index()` – list all models
* `store()` – create a new record
* `show()` – view a specific model
* `update()` – update a record
* `destroy()` – delete a record

Replace the **model name** and **column names** in the generated methods with your specific ones.

> 🎯 This structure allows you to build full CRUD operations quickly with clean, testable code.

---

### 🧩 (Optional) Using Action Classes

To keep your controller clean and your business logic reusable, you can offload complex logic into **Action classes**.

#### Example Structure:

```
app/Action/FilterModel/FilterModelAction.php
```

You can create an action class like `FilterModelAction`, and call it from your controller:

```php
$data = app(FilterModelAction::class)->handle($request);
```

> 💡 This pattern helps keep your controller methods **short, readable, and maintainable**.

Feel free to rename the `FilterModel` folder and class based on your own resource.

---

## ✅ Custom Form Request Validation

Laravel's **Form Request** classes provide a clean way to handle validation.

### 🛠 Generate a Request:

```bash
php artisan make:request Test
```

This creates a file in:

```
app/Http/Requests/Test.php
```

For your model, you might create something like:

```
app/Http/Requests/ModelNameRequest.php
```

### ✏️ Example Use:

Update the rules inside the request:

```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'status' => 'in:PENDING,IN PROGRESS,COMPLETED',
        'due_date' => 'required|date',
    ];
}
```

Then use this request in your controller:

```php
public function store(ModelNameRequest $request)
{
    // $request is already validated
    Model::create($request->validated());
}
```

> 🔐 This approach improves **security**, **code readability**, and **validation reuse**.

---

## 🎨 Blade Integration (Frontend)

Refer to the Blade files in your project that handle:

* Form inputs
* Modal popups
* AJAX calls (already covered earlier)
* Toastr notifications for feedback

Your controller returns standard JSON responses which are handled by your front-end scripts to show modals or success messages.

> 📂 Example views are located in `resources/views`.

---
---