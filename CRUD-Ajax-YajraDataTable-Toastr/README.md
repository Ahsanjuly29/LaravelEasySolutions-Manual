
## 6. jQuery AJAX with Bearer Token

```blade
<meta name="access-token" content="{{ auth()->user()?->currentAccessToken()?->plainTextToken }}">
```

```js
let token = $('meta[name="access-token"]').attr('content');

$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    }
});

$.get('/api/posts', function(posts) {
    console.log(posts);
});
```

> Or store token in `localStorage/sessionStorage` and use globally in JS.

---
















---

# 📚 Laravel CRUD by AJAX, Yajra DataTables & Toastr Alerts

Full-stack setup using Laravel API Resource Controllers, Blade Views, AJAX-based front-end, Yajra DataTables, and Toastr for notifications.

---

## ⚙️ Step 1: Install Laravel

```bash
composer create-project --prefer-dist laravel/laravel myproject
```

or

```bash
laravel new TestProject
```

---

## 📘 Optional: Authentication with Laravel Breeze

You can easily set up Laravel authentication scaffolding **LINK**.

🔗 [Click here to set it up](https://github.com/Ahsanjuly29/LaravelEasySolutions-Manual/tree/main/ApiAuthentications)


---

## 🛠️ Step 2: Create Model, Controller & Migration

```bash
php artisan make:model Company -mcr --api
```

| Flag    | Description                             |
| ------- | --------------------------------------- |
| `-m`    | Create migration                        |
| `-c`    | Create controller                       |
| `-r`    | Create resource controller (CRUD ready) |
| `--api` | API controller (no view methods)        |

---

## 🎨 Step 3: View Files & Frontend Setup

Set up AJAX-based CRUD using Blade + jQuery.

---

### 🧱 Master Layout (resources/views/master/app.blade.php)

#### 🖼️ Head Section (make sure these files exists)


***CS files***

```html
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="bearer-token" content="{{ session('loginToken' . auth()->user()?->id) }}">
    <meta name="app-locale" content="{{ app()->getLocale() }}">

    <title>{{ config('app.name', 'Laravel App') }}</title>

    {{-- tab icon --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">

    {{-- Css --}}
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">

    <!-- Toastr CSS if you use toastr notifications -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Your icon/font CSS if needed -->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/buttons.bootstrap5.min.css') }}">


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- inline css Files  --}}
    @yield('custom-css')
</head>

@yield('main-body')
```

***📜 JS files***

```html
<!-- jQuery -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

<!-- select2 -->
<script src="{{ asset('assets/js/select2.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

<!-- DataTables core -->
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

<!-- DataTables Bootstrap 5 integration -->
<script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Optional: DataTables Buttons extension if needed -->
<script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/js/jszip.min.js') }}"></script>

<script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/js/vfs_fonts.js') }}"></script>

<script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>

<!-- Toastr for notifications -->
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

<!-- custom JS files -->
<script src="{{ asset('assets/js/ajax-dataTable.js') }}"></script>
<script src="{{ asset('assets/js/ajax-crud.js') }}"></script>


{{-- inline Js Files  --}}
@yield('custom-js')
```

---

## ⚡ Step 4: Blade View for AJAX CRUD (`index.blade.php`)

```blade
@extends('master.app')

@include('company.form-modal')

@section('custom-css')
@endsection

@section('main-body')
@endsection

@section('custom-js')


@endsection
```

---

### 🧩 Contents inside `main-body`


---

# 📘 README: JavaScript Configuration for Dynamic CRUD DataTable

---

## 🛠 Global Configuration Object for AJAX operation

The `window.config` object defines **selectors and class names** used across multiple CRUD operations.

```js
    window.config = {
        formId: '#form',                         // Use this as the ID for your <form id="form">
        modalId: '#form-modal',                  // Use this as the ID for your form modal     <div class="modal" id="form-modal">
        titleSelector: '.modal-title',           // Use this as the class for modal title <h5 class="modal-title">
        urlInputId: '#url',                      // Use this as the ID for hidden input <input type="hidden" id="url">
        methodInputName: '_method',              // Use this as the name for HTTP method input <input name="_method">
        submitBtnId: '#formSubmitBtn',           // Use this as the ID for form submit button <button id="formSubmitBtn">
        createBtnClass: '.create-btn',           // Use this as the class for "Create New" button <button class="create-btn">
        editBtnClass: '.edit-btn',               // Use this as the class for edit buttons <button class="edit-btn">
        deleteBtnClass: '.delete-btn',           // Use this as the class for delete buttons <button class="delete-btn">
        checkItemClass: '.checkitem',            // Use this as the class for row checkboxes <input class="checkitem">
        checkAllBoxId: '#check_all_box',         // Use this as the ID for "select all" checkbox <input id="check_all_box">
        multipleDeleteBtnId: '#multiple_delete_btn' // Use this as the ID for bulk delete button <button id="multiple_delete_btn">
    };
```
> 🔄 include these elements in your HTML for functionality.

---

## 🔄 Select2 with AJAX Integration

### 🔧 How It Works

All elements with `.select2-ajax` will be initialized with AJAX-based searching from a remote URL.

```js
const initSelect2WithAjaxCall = ($context) => {
    $context.find('.select2-ajax').each(function() {
        const $select = $(this);
        const url = $select.data('url');
        ...
    });
};
```

### ✅ Required HTML Example

```html
<select class="select2-ajax"
        data-url="{{ route('api.dropdown.options') }}"
        data-placeholder="Select a company">
</select>
```

### 💡 Features

* Lazy loads options via AJAX
* Uses a placeholder
* Avoids re-initialization

> ⚠ Ensure `ajaxCall()` is defined globally to support this request. It should trigger the `success()` callback on success.

---

1. **Add Required HTML Elements**

   * Table with correct `data-` attributes
   * Modal and form with matching `#form`, `#form-modal`, etc.
   * Select inputs with `.select2-ajax` class and `data-url`

---

## 📊 DataTable Configuration

The `DataTableManager.init({...})` call initializes a **feature-rich DataTable** with:

* AJAX loading
* CRUD action buttons
* Export buttons (Copy, Print, Excel, PDF)
* Bulk deletion

### ✅ Required HTML

Your table must look like this:

```html
<table id="data_table"
       data-url="{{ route('your-data-fetch-route') }}"
       data-csrf="{{ csrf_token() }}"
       data-bulk-delete-url="{{ route('your.bulk.delete.route') }}">
</table>
```

### 📁 Table Columns Setup

```js
columns: [
    { data: null, name: 'checkbox', render: ..., className: "text-center" },
    { data: null, name: 'serial_number', render: ..., className: "text-center" },
    { data: 'name', name: 'name' },
    { data: 'slug', name: 'slug' },
    { data: null, render: ..., orderable: false }
]
```

* **Checkbox column**: Enables multi-row selection
* **Serial number**: Auto-incremented row number
* **Name / Slug**: Actual data fields
* **Actions**: Edit/Delete buttons with proper `data-url`

> 🔗 The `data-url` attributes are dynamically populated using `baseShowUrl` and `baseDestroyUrl`.

---

## 📁 Export Options

```js
exportColumns: [1, 2, 3],
buttons: [
    { extend: 'copy' },
    { extend: 'print' },
    { extend: 'excel' },
    { extend: 'pdfHtml5' }
]
```
These allow users to export visible data in various formats. Columns 1, 2, 3 refer to `serial_number`, `name`, and `slug`.

***✅ To get full code see views/company/index.blade.php***

---

## 📦 Global Setup inside blade file

add edit/show and delete URLs dynamically generated from Laravel's named routes:

```js
const baseShowUrl = "{{ route('company-api.show', ':id') }}";
const baseDestroyUrl = "{{ route('company-api.destroy', ':id') }}";
```

These are templates for RESTful **Show** and **Delete** operations. it will `.replace(':id', yourId)` with the actual ID.

---

## 🌐 Step 5: Define Routes

### 📄 Web Routes (`routes/web.php`)

```php
Route::middleware('auth')->get('/company', function () {
    return view('company.index', [
        'allData' => Company::orderBy('id', 'DESC')->paginate(10)
    ]);
})->name('company.index');
```

### 🔗 API Routes (`routes/api.php`)

```php
Route::middleware(['auth:sanctum'])->resource('api-company-data', ModelNameController::class);
```

> 🧱 **Laravel 12 Note:**
> If `routes/api.php` doesn't exist:

```bash
php artisan install:api
```

---

## 🧬 Migration Columns (Modify as Needed)

```php
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

---

## 🧾 Fillable / Guarded

```php
protected $fillable = ['name'];
// OR
protected $guarded = ['id'];
```

---

## 🔗 Relationships (Example)

```php
public function relationshipCallerName()
{
    return $this->belongsTo(Company::class, 'foreign_key', 'primary_key');
}
```

---

## 🔍 Scope (Optional)

```php
public function scopeOwner(Builder $query): void
{
    $query->with('assignedTo', 'createdBy')->whereAny(['assigned_to', 'created_by'], Auth::user()->id);
}
```

---

# ✅ API Section & Architecture

---

## 📁 Folder Structure

```
app/
├── Actions/
│   └── ModelApi/
│       └── FilterModels.php
├── Helpers/
│   ├── custom/
│   │   ├── Validation.php
│   │   └── ApiCustomResponse.php
│   └── helpers.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── YourController.php
│   ├── Requests/
│   │   └── TaskRequest.php
├── Traits/
│   └── IsValidRequest.php
```

---

## 🧭 Request Validation (`TaskRequest.php`)

```php
use IsValidRequest;

public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'name' => 'required',
        'description' => 'nullable',
        'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED',
        'due_date' => 'required|date_format:Y-m-d',
    ];
}
```

---

## 🧬 Trait (`IsValidRequest.php`)

```php
namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait IsValidRequest
{
    public function validationData()
    {
        try {
            // Checking if the request is valid api request or not.
            isApiRequestValidator($this);
            return $this->all();
        } catch (\Exception $e) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], 422)
            );
        }
    }

    /**
     * Function that rewrites the parent method and throwing
     * custom exceptions of validation.
     */
    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $validator->getMessageBag()->toArray(),
                    'errors' => $validator->errors(),
                ], 422)
            );
        }
    }
}

```

---

## 🧰 Controller Methods

### 📥 Index

```php
public function index(Request $request, FilterModel $filterModel)
{
    try {
        $data = $filterModel->handle($request, 100);
        return successResponse('Showing All Data', $data);
    } catch (\Exception $e) {
        return errorResponse($e);
    }
}
```

### 📝 Store

```php
public function store(TaskRequest $request)
{
    try {
        $data = $request->validated();
        $data['created_by'] = $data['assigned_to'] = Auth::user()->id;
        $allData = ModelName::create($data);
        return successResponse('New Task has been Created', $allData);
    } catch (\Exception $e) {
        return errorResponse($e);
    }
}
```

### 🔍 Show / Edit

```php
public function show($id)
{
    try {
        $taskData = ModelName::find($id);
        if (empty($taskData)) {
            throw new \Exception('Unable to Find This Task');
        }

        $taskData['url'] = route('api-task.update', $id);
        $taskData['due_date'] = date('Y-m-d', strtotime($taskData->due_date));
        $taskData->unsetRelation('assignedTo')->unsetRelation('createdBy');

        unset($taskData->created_by, $taskData->assigned_to);

        return successResponse('Open Modal', $taskData);
    } catch (\Exception $e) {
        return errorResponse($e);
    }
}

public function edit($id)
{
    return $this->show($id);
}
```

### 🔁 Update

```php
public function update(TaskRequest $request, string $id)
{
    try {
        $taskData = ModelName::find($id);
        if (empty($taskData)) {
            throw new \Exception('Unable to Find This Task');
        }

        $taskData->update($request->validated());

        return successResponse('This Task has been Updated', $taskData);
    } catch (\Exception $e) {
        return errorResponse($e);
    }
}
```

### ❌ Destroy

```php
public function destroy(Request $request)
{
    try {
        ModelName::whereIn('id', explode(',', $request->ids))->delete();
        return successResponse('This Task has been Destroyed');
    } catch (\Exception $e) {
        return errorResponse($e);
    }
}
```

---

## ⚙️ Filter Action Class

**`App\Actions\ModelApi\FilterModels.php`**

```php
namespace App\Actions\ModelApi;

use App\Models\ModelName;

class FilterModels
{
    public function handle($request, $dataAmount)
    {
        $taskData = ModelName::query();

        if (! empty($request->status)) {
            $taskData->where('status', $request->status);
        }

        if (! empty($request->searchName)) {
            $taskData->where('name', 'like', '%' . $request->searchName . '%');
        }

        return $taskData->orderBy('due_date', 'ASC')->paginate($dataAmount);
    }
}
```

---

## 🛠️ Helper File Setup

### 🔧 1. Create `app/Helpers/helpers.php`

```bash
touch app/Helpers/helpers.php
```

### ➕ 2. Include Logic

```php
require_once 'custom/Validation.php';
require_once 'custom/ApiCustomResponse.php';
```

### 🔁 3. Register in `composer.json`

```json
"autoload": {
    "files": [
        "app/Helpers/helpers.php"
    ]
}
```

### 🚀 4. Dump Autoload

```bash
composer dump-autoload
```

---

## ✅ Sample JSON Response

```json
{
  "status": 1,
  "message": "New company has been created",
  "data": {
    "id": 1,
    "name": "company 1",
    "due_date": "2025-07-14"
  },
  "data": {
    "id": 2,
    "name": "company 2",
    "due_date": "2025-07-14"
  }
}
```

---
