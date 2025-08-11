
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

















# 📚 Laravel CRUD using AJAX, Toastr Alerts

Full-stack setup using Laravel API Resource Controllers, Blade Views, AJAX-based front-end, Yajra DataTables, and Toastr for notifications.

---

## ⚙️ Optional 1: Install Laravel(if making new project)

```bash
composer create-project --prefer-dist laravel/laravel myproject
```
or
```bash
laravel new TestProject
```

---

## 📘 Optional 2: Authentication with Laravel Breeze

authentication scaffolding **LINK**.

🔗 [Click here for details](hhttps://github.com/Ahsanjuly29/LaravelEasySolutions-Manual/blob/main/Api-Authentications)


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

Set up your template(bootstrap or any html, css template). common base then accodring to page

---

### 🧱 Master Layout (resources/views/master/app.blade.php)

#### 🖼️ Head Section (add these common files atleast)


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

<!-- Toastr for notifications -->
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

<!-- Custom made Crud through AJAX -->
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

## 📊 Table Configuration

* AJAX loading
* CRUD action buttons
* Export buttons (Copy, Print, Excel, PDF)
* Bulk deletion

### ✅ Required HTML

Your table must look like this:

```blade

    <button type="button"
        class="nav-link border border-primary w-100 btn btn-primary create-task"
        data-url="{{ route('api-task.store') }}">
        Create
    </button>
    <table data-url="{{ route('your-data-fetch-route') }}" data-csrf="{{ csrf_token() }}"
        data-bulk-delete-url="{{ route('api-company-data.destroy', 0) }}">
        <thead>
            <th class="text-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="check_all_box" />
                </div>
            </th>
            ...
            ...
            ...
            <th>Action</th>
        </thead>

        <tbody>
            @foreach ($allData as $item)
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input checkitem" type="checkbox" value="{{ $item->id }}"
                                name="id" />
                        </div>
                    </td>
                    ...
                    ...
                    ...
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-outline-primary me-1 edit-task"
                                data-url="{{ route('api-company-data.edit', $item->id) }}"> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger ms-1 delete-btn"
                                data-url="{{ route('api-company-data.destroy', $item->id) }}" data-id="{{ $item->id }}">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="13">
                    <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none" type="submit"
                        data-url="{{ route('api-task.destroy', 0) }}">
                        Delete all
                    </button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="100">
                    {!! $tasks->render() !!}
                </td>
            </tr>
        </tfoot>
    </table>
```

---

## 🛠 Global Configuration Object for AJAX operation

The `window.config` object defines **selectors and class names** used across multiple CRUD operations.

```js
    // Ajax Configaraton 
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

```blade
    <!-- The Modal -->
    <div class="modal" id="form-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Create Form</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="card-body">
                        <form id="form" method="POST">
                            <div class="row">
                                <div class="col-sm-4 mt-1 ">
                                    <input type="text" class="form-control" placeholder="Enter name" id="name"
                                        name="name">
                                </div>
                                <div class="col-sm-4 mt-1 ">
                                    <select
                                        class="select2-ajax form-select"
                                        name="status" id="status">
                                        <option value="PENDING">PENDING</option>
                                        <option value="IN_PROGRESS">IN PROGRESS</option>
                                        <option value="COMPLETED">COMPLETED</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Add hidden url input for create/Update form -->
                            <input type="hidden" id="url" value="">
                        </form>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button class="btn btn-outline-primary" id="formSubmitBtn">Submit</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"
                        id="formReset">Close</button>
                </div>
            </div>
        </div>
    </div>
```


---

## 🔄 Dynamic Select2 with AJAX Integration(if needed)

When using Select2 with AJAX, structure your `<select>` element like this:

* `data-url` → API endpoint that returns JSON data for the dropdown.
* `data-id-field` → The unique key from each item in the response (used as the `<option value="">`).
* `data-text-field` → The field used to display text in the dropdown.
* `data-placeholder` → Placeholder text shown before an option is selected.

```html
    <select
        class="select2-ajax"                              
        data-url="{{ route('api.dropdown.options') }}"    
        data-id-field="id"                                
        data-text-field="name"                            
        data-placeholder="Select a Company">
    </select>
```

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

Route::middleware(['auth:sanctum'])->group(function () {ßß
    Route::resource('api-company-data', ModelNameController::class);    
});

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

## 🧰 API Controller Methods

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
