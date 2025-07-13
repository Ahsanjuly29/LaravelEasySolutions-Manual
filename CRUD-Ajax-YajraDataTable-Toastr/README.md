
---

# 📚 Laravel CRUD with AJAX, Yajra DataTables & Toastr Alerts

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

🔗 **Click Here (Link)** — to setup Laravel Breeze for authentication scaffolding.

---

## 🛠️ Step 2: Create Model, Controller & Migration

```bash
php artisan make:model ModelName -mcr --api
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

#### 🖼️ Head Section

```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="bearer-token" content="{{ session('loginToken' . auth()->user()?->id) }}">

    <title>{{ config('app.name', 'Laravel App') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">

    @yield('custom-css')
</head>

@yield('main-body')
```

#### 📜 Script Section

```blade
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>

@yield('custom-js')
```

---

## ⚡ Step 4: Blade View for AJAX CRUD (`index.blade.php`)

```blade
@extends('master.app')

@include('ajax.form-modal')

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

### 🧩 Contents inside `main-body`

#### 🔘 Create Button

```blade
<button type="button" class="border border-primary w-100 btn btn-primary create-btn"
    data-url="{{ route('api-model-name.store') }}">
    Create
</button>
```

#### 📊 DataTable View

```blade
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="check_all_box" />
                    </div>
                </th>
                <th>Id</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allData as $item)
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input checkitem" type="checkbox"
                                value="{{ $item->id }}" name="id" />
                        </div>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name ?? '--' }}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                data-url="{{ route('api-model-name.edit', $item->id) }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger ms-1 delete-btn"
                                data-url="{{ route('api-model-name.destroy', $item->id) }}"
                                data-id="{{ $item->id }}">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="13">
                    <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none"
                        type="submit" data-url="{{ route('api-model-name.destroy', 1) }}">
                        Delete all
                    </button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="100">{!! $allData->render() !!}</td>
            </tr>
        </tfoot>
    </table>
</div>
```

---

## 🌐 Step 5: Define Routes

### 📄 Web Routes (`routes/web.php`)

```php
Route::middleware('auth')->get('/ajax-crud', function () {
    return view('ajax.index', [
        'allData' => ModelName::orderBy('id', 'DESC')->paginate(10)
    ]);
});
```

### 🔗 API Routes (`routes/api.php`)

```php
Route::middleware(['auth:sanctum'])->resource('api-model-name', ModelNameController::class);
```

> 🧱 **Laravel 12 Note:**
> If `routes/api.php` doesn't exist:

```bash
php artisan install:api
```

---

## 🧬 Migration Columns (Modify as Needed)

```php
Schema::create('models', function (Blueprint $table) {
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
    return $this->belongsTo(Model::class, 'foreign_key', 'primary_key');
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
            isApiRequestValidator($this);
            return $this->all();
        } catch (\Exception $e) {
            throw new HttpResponseException(
                response()->json(['status' => 0, 'message' => $e->getMessage()], 422)
            );
        }
    }

    public function failedValidation(Validator $validator
```


)
{
throw new HttpResponseException(
response()->json(\[
'status' => 0,
'message' => \$validator->getMessageBag()->toArray(),
'errors' => \$validator->errors(),
], 422)
);
}
}

````

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
````

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
  "message": "New Task has been Created",
  "data": {
    "id": 1,
    "name": "Sample Task",
    "due_date": "2025-07-14"
  }
}
```

---
