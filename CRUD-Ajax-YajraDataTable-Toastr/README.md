Absolutely! Here's the updated `README.md`-style documentation, following your instructions:

* Removed the Sanctum installation section
* Commented out the relationship and scope code examples inside the model
* Added a titled step for structuring reusable logic via Laravel **Action** classes, including a file structure and simple example

---

# 📘 **CRUD Operations with AJAX & Yajra DataTables in Laravel**

This guide explains how to implement **AJAX-based CRUD operations** in Laravel using **Yajra DataTables** and **Toastr.js** for dynamic front-end interactions and user notifications.

---

## 🛠️ **Installation**

### 1. Install Laravel (If not already installed)

```bash
composer create-project --prefer-dist laravel/laravel myproject
```

---

## ⚙️ **Project Setup**

### 1. Create Model, Controller & Migration

```bash
php artisan make:model ModelName -mcr
```

> Options:
>
> * `-m`: Generate migration
> * `-c`: Generate controller
> * `-r`: Use resource controller (CRUD methods)

---

### 2. Define Routes

#### `routes/web.php`

```php
use App\Models\ModelName;

Route::middleware('auth')->get('/ajax-crud', function () {
    return view('ajax.index', [
        'allData' => ModelName::orderBy('id', 'DESC')->paginate(10)
    ]);
});
```

#### `routes/api.php`

```php
use App\Http\Controllers\ModelNameController;

Route::middleware(['auth'])->resource('api-model-name', ModelNameController::class);
```

---

## 🧱 **Migration Setup**

After generating the model, locate the corresponding migration file and define your database schema:

```php
public function up()
{
    Schema::create('model_names', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });
}
```

Run the migration:

```bash
php artisan migrate
```

---

## 🧩 **Model Configuration**

In your model file (`app/Models/ModelName.php`):

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelName extends Model
{
    protected $fillable = ['title']; // Add your fields here

    // Example relationship (commented out)
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    // Example scope (commented out)
    // public function scopeOwner($query)
    // {
    //     $query->where('user_id', auth()->id());
    // }
}
```

---

## 🧨 Step 3: Organize Logic Using Action Classes

Instead of writing everything in the controller, you can keep things clean and testable using Laravel's Action structure. For example, you can create an **`App\Actions\ModelName\CreateModelName.php`** class to handle creation logic.

### 📁 File Structure Example

```
app/
├── Actions/
│   └── ModelName/
│       ├── CreateModelName.php
│       ├── UpdateModelName.php
│       └── DeleteModelName.php
```

### 🧾 Example: `CreateModelName.php`

```php
namespace App\Actions\ModelName;

use App\Models\ModelName;

class CreateModelName
{
    public function handle(array $data): ModelName
    {
        return ModelName::create($data);
    }
}
```

### 🔄 Usage in Controller

```php
use App\Actions\ModelName\CreateModelName;

public function store(Request $request)
{
    $model = (new CreateModelName)->handle($request->all());
    return response()->json(['success' => true, 'data' => $model]);
}
```

> This keeps your controllers thin and actions reusable.

---

## 📊 **Integrating DataTable + AJAX**

### 1. Blade Setup (`layouts/master.blade.php` or equivalent)

```blade
<!-- Meta -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="bearer-token" content="{{ session('loginToken' . auth()->user()->id) }}">

<!-- Styles -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
```

### 2. JavaScript Dependencies

Include these in your layout:

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

<!-- Custom AJAX Logic -->
<script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>
```

---

## 📥 **Form Requests**

Use Laravel Form Request validation to clean up controllers:

```bash
php artisan make:request StoreModelNameRequest
```

Inside `StoreModelNameRequest.php`:

```php
public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
    ];
}
```

Then use it in your controller:

```php
public function store(StoreModelNameRequest $request)
{
    $model = ModelName::create($request->validated());
    return response()->json(['success' => true, 'data' => $model]);
}
```

---

## 🖥️ **Blade Implementation**

Create your view (`resources/views/ajax/index.blade.php`):

```blade
<table id="datatable" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
```

In your `ajax-jquery-crud.js` file:

```javascript
$('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('your.route') }}",
    columns: [
        { data: 'id', name: 'id' },
        { data: 'title', name: 'title' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
});
```

---

## ✅ **Conclusion**

You’ve now built a working **AJAX-based Laravel CRUD system** using:

* Laravel Resource Controllers
* DataTables for listing and filtering
* Toastr for clean alerts
* Action classes for reusable logic

---

## 📚 **Further Reading**

* [Laravel Docs](https://laravel.com/docs)
* [Yajra Laravel DataTables](https://yajrabox.com/docs/laravel-datatables)
* [Toastr.js](https://codeseven.github.io/toastr/)

---

Let me know if you'd like this exported as a `.md` file or turned into Laravel-style Blade documentation!
