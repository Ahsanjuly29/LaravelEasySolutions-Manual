# 📚 Laravel CRUD using AJAX. Using Toastr Alerts

Laravel API Resource Controllers, Blade Views, AJAX-based front-end, and Toastr notifications.

---

## ⚙️ Optional 1: Install Laravel(Creating new project)

```bash
composer create-project --prefer-dist laravel/laravel NewProject
```
or
```bash
laravel new NewProject
```

---

## 📘 Optional 2: Authentication with Laravel Breeze

authentication scaffolding **LINK**.

🔗 [Click here for details](https://github.com/Ahsanjuly29/LaravelEasySolutions-Manual/blob/main/Api-Authentications)


---

## 🛠️ Step 1: Create Model, Controller & Migration

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

## step 2: migration and modal relationship setup

### 🧬 Migration Columns (example)

```php
    Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
```

---

## 🧾 On model add Fillable or Guarded (both if needed)

```php
    protected $fillable = ['name'];
    // OR
    protected $guarded = ['id'];
```

---

## 🔗 Define relationships inside model (Example)

```php
    public function relationshipCallerName()
    {
        return $this->belongsTo(Company::class, 'foreign_key', 'primary_key');
    }
```

---

## 🔍 Scope (Optional)

```php
public function scopeOwner(Builder $query): Builder
{
    return $query->with('assignedTo', 'createdBy')
                 ->whereAny(['assigned_to', 'created_by'], Auth::id());
}

```


## 🎨 Step 3: View Files & Frontend Setup

Set up your template(bootstrap or any html, css template). common base then accodring to page

---

### 🧱 Setup view (After Login) pages using blade

#### 🖼️ For Admin LTE 3 [click here](AdminLTE3)

Here all the needed files(css, js) are available also blade formatted. 

#### ajax-jquery-crud.js
i have created a custom file for CRUD operation using AJAX. [ajax-jquery-crud.js](ajax-jquery-crud)
add this file to. your js and add this to ur js folder.

```blade
    <script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>
```

---

## ⚡ Step 4: Table View (`index.blade.php`)

after setting up template common files, lets setup our table contents. 

## 🧩 Pages Structure setup. Connect common page with every other pages.

```blade
@extends('app')

@include('form-modal') // insluding a modal page here

@section('custom-css')
// if any other css is needed for this page only can declare here 
@endsection

@section('main-content')

// main content will be written here. if its table, dashboard contentsm form, this page main contents will be here.
@endsection

@section('custom-js')
// if any other js code is needed ...
@endsection
```
---

## Example table page for company page
Table page [click here](index.blade.php)
Form modal [click here](form-modal.blade.php) for create/edit data. using this same modal, can store/update data.


## 🛠 Global Configuration Object for AJAX operation

`window.config` object defines **selectors and class names** used across multiple CRUD operations. its already defined in js file but in case if need to change any class names on any page or module still can use same my ajax crud function by defining this. 

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
---

## 🔄 Dynamic Select2 with AJAX Integration(if needed)

When using Select2 with AJAX, structure your `<select>` element like this:

* `data-url` → API endpoint that returns JSON data for the dropdown. if no data provided it will show written static options.
* `data-id-field` → The unique key from each item in the response (used as the `<option value="">`).
* `data-text-field` → The field used to display text in the dropdown.
* `data-placeholder` → Placeholder text shown before an option is selected.

```html
    <select
        class="form-control select2-ajax"                              
        data-url="{{ route('api.path') }}"    
        data-id-field="id"                                
        data-text-field="name"                            
        data-placeholder="Select a Company">
    </select>
```

---

# ✅ Controller, API Section & Architecture

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

## FrontendController
create this FrontendController so u can redirect to pages. as this will be totally api based but blade based also. so its better to use a controller for redirecting to targetted page

```bash
    php artisan make:controller FrontendController
```

## 🧰 API Controller Methods

Add this [file](Controller/Api/CompanyController.php) to project directly or can open file to get help

---

## 🧭 Custom Request Validation

### Create custom Request File
```bash
    php artisan make:request CompanyRequest
```

This will create file like this. attatched the request [file here](Request)

```php
    use IsValidRequest; // use trait for override methods

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // write rules for each inputs, like,
        return [
            'name' => 'required|string|max:255'
        ];
    }
```

---

## 🧬 Trait (`IsValidRequest.php`)

Create a folder inside App named Traits. then create a file named IsValidRequest.php.
this validationData and failedValidation are overriding functions. here validationData checks if data is actually a json if yes then it will send a custom format response. that is needed for api responses. same for failedValidation. they are mainly override for json responses

get file [here](Traits/IsValidRequest.php)

---

## 🛠️ Helper File Setup

### 🔧 1. Create folder `Helpers` Folder and a file `helpers.php` inside App folder

inside Helpers Folder create php files. then include_once those files inside helpers.php file.

```php
    include_once('Helpers/ApiDataResponse.php');
    include_once('Helpers/AuthToken.php');
    include_once('Helpers/Navbar.php');
    include_once('Helpers/Validation.php');
```

### 🔁 3. Register `helpers.php` in `composer.json`

```json
    "autoload": {
        "files": [
            "app/helpers.php"
        ]
    }
```

### 🚀 4. Dump Autoload

```bash
    composer dump-autoload
```

---

inside traits a helper function was called `isApiRequestValidator` there it checks if request is json or not. can include all helpers function [click here](Helpers)


## ⚙️ Filter Action Class

inside controller index function there is a option for filter data. that is handled using Action.

### first create a Folder `Action` then inside create a file `FilterModels.php`

Can use [this Actions](Actions) file, 

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('api-company-data', ModelNameController::class);    
});

```

---

## ✅ Sample JSON Response

```json
{
  "status": 1,
  "message": "Companies fetched successfully",
  "data": [
    {
      "id": 1,
      "name": "company 1",
      "due_date": "2025-07-14"
    },
    {
      "id": 2,
      "name": "company 2",
      "due_date": "2025-07-14"
    }
  ]
}

```

---
