# 📋 Dynamic AJAX CRUD Task Management using LARAVEL API With BEARER TOKEN

This Laravel project implements AJAX-based CRUD operations for managing tasks. The project includes dynamic modal forms for creating and editing tasks, a multi-delete feature, and integrates with Laravel's API routes for back-end processing.

## 📑 Table of Contents

- [✨ Features](../../../../../../d:/xampp/htdocs/TaskManagementSystem2/README.md#features)
- [⚙️ Installation](../../../../../../d:/xampp/htdocs/TaskManagementSystem2/README.md#installation)
- [🖥️ Backend API Setup](../../../../../../d:/xampp/htdocs/TaskManagementSystem2/README.md#backend-api-setup)
- [🌐 Frontend Setup](../../../../../../d:/xampp/htdocs/TaskManagementSystem2/README.md#frontend-setup)
- [🚀 Usage](../../../../../../d:/xampp/htdocs/TaskManagementSystem2/README.md#usage)
- [📜 License](../../../../../../d:/xampp/htdocs/TaskManagementSystem2/README.md#license)

## ✨ Features

- **AJAX CRUD Operations**: Perform create, read, update, and delete operations without page reloads.
- **Dynamic Modals**: Unified forms for creating and editing tasks within a modal.
- **Multi-Delete Functionality**: Allows multiple task deletion.
- **CSRF and Bearer Token Security**: Uses Laravel's built-in security tokens for AJAX requests.

## ⚙️ Installation

1. **Clone the Repository**  
   ```bash
   git clone <repository-url>
   cd project-directory
   ```

2. **Install Dependencies**  
   ```bash
   composer install
   npm install
   npm run dev
   ```

3. **Configure Environment**  
   Copy `.env.example` to `.env`, then configure database and other environment settings.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run Migrations**  
   ```bash
   php artisan migrate
   ```

## 🖥️ Backend API Setup

1. **Create Model, Controller, and Migration for Tasks**  
   ```bash
   // Can create it at once and use resource controller for faster development, Command:
   php artisan make:model Task -mcr
   ```
   Update the migration file to add necessary columns for tasks:

   ```php
   Schema::create('tasks', function (Blueprint $table) {
       $table->id();
       $table->string('name');
       $table->longText('description')->nullable();
       $table->string('status')->default('PENDING')->comment('PENDING', 'IN_PROGRESS', 'COMPLETED');
       $table->unsignedBigInteger('assigned_to');
       $table->unsignedBigInteger('created_by');
       $table->dateTime('due_date');
       $table->timestamps();
   });

   Schema::table('tasks', function (Blueprint $table) {
       $table->foreign('assigned_to')->references('id')->on('users');
       $table->foreign('created_by')->references('id')->on('users');
   });
   ```


2. **Define Relationships in the Model**  
   In `Task.php`, add the `fillable` attributes and define relationships as needed.

```php
    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;

    class Task extends Model
    {
        use HasFactory;

        protected $fillable = [
            'name',
            'description',
            'status',
            'due_date',
            'created_by',
            'assigned_to'
        ];

        public function assignedTo()
        {
            return $this->belongsTo(User::class, 'assigned_to', 'id');
        }

        public function createdBy()
        {
            return $this->belongsTo(User::class, 'created_by', 'id');
        }

        public function scopeOwner(Builder $query): void
        {
            $query->with('assignedTo', 'createdBy')->whereAny(['assigned_to', 'created_by'], Auth::user()->id);
        }
    }
```

3. **Set up API Routes**
    Here i created another Controller for API responses. u can use Upper one(TaskController) 
   Define routes in `api.php` using a controller, e.g., `ApiTaskController`:
   ```php
    // Task Controller
    Route::middleware(['auth:sanctum'])->group(function () {
        // Task management Routes Resource Controller
        Route::resource('api-task', ApiTaskController::class);
    });
   ```

4. **Controller Logic**  
   Implement CRUD logic in `ApiTaskController.php`. Below is an example `index` method:

   ```php
        namespace App\Http\Controllers\ApiAuth;

        use App\Actions\TaskApi\FilterTasks;
        use App\Http\Controllers\Controller;
        use App\Http\Requests\TaskRequest;
        use App\Models\Task;
        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Auth;

        class ApiTaskController extends Controller
        {
            /**
            * Display all listing of task.
            *
            * @response
            */
            public function index(Request $request, FilterTasks $filterTasks)
            {
                try {
                    $data = $filterTasks->handle($request, 100); // dataAmount

                    return successResponse('Showing All Tasks', $data);
                } catch (\Exception $e) {
                    return errorResponse($e);
                }
            }

            /**
            * Store new task.
            */
            public function store(TaskRequest $request)
            {
                try {
                    $data = $request->validated();
                    $data['created_by'] = $data['assigned_to'] = Auth::user()->id;

                    $taskData = Task::create($data);

                    return successResponse('New Task has been Created', $taskData);
                } catch (\Exception $e) {
                    return errorResponse($e);
                }
            }

            /**
            * Display the specified task.
            */
            public function show($id)
            {
                try {
                    $taskData = Task::owner()->find($id);
                    if (empty($taskData)) {
                        throw new \Exception('Unable to Find This Task');
                    }

                    $taskData['url'] = route('api-task.update', $id);
                    $taskData['due_date'] = date('Y-m-d', strtotime($taskData->due_date));
                    $taskData->unsetRelation('assignedTo')->unsetRelation('createdBy');

                    // Removing Unnecessary Data
                    unset($taskData->created_by);
                    unset($taskData->assigned_to);

                    return successResponse('Open Modal', $taskData);
                } catch (\Exception $e) {
                    return errorResponse($e);
                }
            }

            public function edit($id)
            {
                return $this->show($id);
            }

            /**
            * Update task in DB.
            */
            public function update(TaskRequest $request, string $id)
            {
                try {
                    $taskData = Task::owner()->find($id);
                    if (empty($taskData)) {
                        throw new \Exception('Unable to Find This Task');
                    }

                    $taskData->update($request->validated());

                    return successResponse('This Task has been Updated', $taskData);
                } catch (\Exception $e) {
                    return errorResponse($e);
                }
            }

            /**
            * Remove the Task from storage.
            */
            public function destroy(Request $request)
            {
                try {
                    Task::owner()->whereIn('id', explode(',', $request->ids))
                        ->delete();
                    return successResponse('This Task has been Destroyed');
                } catch (\Exception $e) {
                    return errorResponse($e);
                }
            }
        }
   ```

5. **Custom Requests and Validation**  
   You can Create a custom request file, e.g., `TaskRequest.php`, to handle validation or use Default Request add validation rules on controller. but i like to make clean Controller:

   ```php
        namespace App\Http\Requests;

        use App\Traits\IsValidRequest;
        use Illuminate\Foundation\Http\FormRequest;

        class TaskRequest extends FormRequest
        {
            use IsValidRequest;

            /**
            * Determine if the user is authorized to make this request.
            */
            public function authorize(): bool
            {
                return true;
            }

            /**
            * Get the validation rules that apply to the request.
            *
            * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
            */
            public function rules(): array
            {
                return [
                    'name' => 'required',
                    'description' => 'nullable',
                    'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED',
                    'due_date' => 'required|date_format:Y-m-d',
                ];
            }
        }
   ```

   I am using custom Request, and authentication to accesss this Api.

   I am Using a trait `IsValidRequest` to handle validation responses in JSON format, ensuring all API responses are standardized.

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
   I am Using a helper `isApiRequestValidator`;
    Function that checkes the api request is valid or not.
    Globally diclared as because use of this function will be used in all the requests
    and in the respective functions.

```php
    if (! function_exists('isApiRequestValidator')) {
        /**
         * Function that checkes the api request is valid or not.
         * Globally diclared as because use of this function will be used in all the requests
         * and in the respective functions.
         */
        function isApiRequestValidator($request)
        {
            try {
                $jsonCheck = $request->wantsJson();
                if (! $jsonCheck) {
                    throw new Exception('Invalid Request');
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
```

   **Another HELPER created too for standarize json response.
   ```php
<?php

// 🌟 Custom Helpers for Standardized JSON Responses

// ✅ Custom Success Response
if (!function_exists('successResponse')) {
    /**
     * Generates a standardized success response.
     *
     * @param string $message The success message to return.
     * @param array $data Optional additional data to include in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response with success status.
     */
    function successResponse($message, $data = [])
    {
        return response()->json([
            'success' => 1,
            'message' => $message,
            'data' => $data,
        ], 200);
    }
}

// ❌ Custom Error Response for Production and Development
if (!function_exists('errorResponse')) {
    /**
     * Generates a standardized error response.
     *
     * @param \Exception $e The exception that occurred.
     * @param string $message Optional custom message for the error.
     * @param int $code The HTTP status code for the response (default: 422).
     * @return \Illuminate\Http\JsonResponse The JSON response with error status.
     */
    function errorResponse($e, $message = '', $code = 422)
    {
        // For production, return a generic error message.
        // For local or development environments, provide detailed error information.
        if (app()->environment('production')) {
            return response()->json([
                'status' => 0,
                'message' => !empty($message) ? $message : 'Something went wrong! Failed to complete this action.',
                'data' => [],
            ], $code);
        } else {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage() . '. Line: ' . $e->getLine() . '. File: ' . $e->getFile(),
                'data' => [],
            ], $code);
        }
    }
}

   ```

6. **Using Action Filter Logic (Optional)**  
   To simplify controller logic, you may use Actions such as `FilterTasks`:
   ```php
        namespace App\Actions\TaskApi;
        use App\Models\Task;
        class FilterTasks
        {
            public function handle($request, $dataAmount)
            {
                // there is not administration Control. So, Each person can see only his/her own tasks.
                $taskData = Task::owner();

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
##Backend or AP{i}s  Process Complete

## Overview

The system allows users to create, read, update, and delete tasks without refreshing the page, enhancing the user experience.

## 🌐 Frontend Setup

### 1. Define Routes

Add the following route to your `web.php` file to redirect to the view page and apply the middleware group for authentication:

```php
use App\Models\Task;

Route::get('/ajax-crud', function () {
    return view('ajax.index', [
        'tasks' => Task::orderBy('id', 'DESC')->paginate(10)
    ]);
})->name('ajax-crud')->middleware('auth');
```

### 2. Create the View Page

Create a new view file named `index.blade.php` in the `resources/views/ajax` folder. This file will display the tasks and include buttons for CRUD operations.

### index.blade.php

```blade
@extends('master.app')
@section('custom-css')
@endsection

@section('main-body')
    <div class="container">
        <div class="card">
            <div class="card-header">Manage Tasks</div>
            <div class="card-body">
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
                                <th>Status</th>
                                <th>Name</th>
                                <th>Assigned To</th>
                                <th>Created By</th>
                                <th>Due</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input checkitem" type="checkbox"
                                                value="{{ $task->id }}" name="id" />
                                        </div>
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->status ?? '--' }}</td>
                                    <td>
                                        {{ $task->name ?? '--' }}
                                    </td>
                                    <td>
                                        {{ $task->assignedTo->name ?? '--' }}
                                    </td>
                                    <td>
                                        {{ $task->createdBy->name ?? '--' }}
                                    </td>
                                    <td>
                                        {{ date('D Y-m-d H:i', strtotime($task->due_date)) ?? '--' }}
                                    </td>
                                    <td>
                                        {{ date('D Y-m-d H:i', strtotime($task->created_at)) ?? '--' }}
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-sm btn-outline-primary me-1 edit-task"
                                                data-url="{{ route('api-task.edit', $task->id) }}"> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger ms-1 delete-btn"
                                                data-url="{{ route('api-task.destroy', $task->id) }}"
                                                data-id="{{ $task->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="13">
                                    <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none"
                                        type="submit" data-url="{{ route('api-task.destroy', 1) }}">
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
                </div>
            </div>
        </div>
    </div>
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

### app.blade.php

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="bearer-token" content="{{ session('loginToken' . auth()->user()->id) }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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

<body class="font-sans antialiased">

    <div class="container border border-dark border-2 rounded p-0">
        <div class="p-5 pb-3 bg-secondary text-white text-center">
            <h1>Task Management System</h1>
            <p>Create, Update , Read or Delete through Ajax & Jquery</p>
            <p class="m-0">
                <b>
                    {{ Auth::user()->name }}
                </b>
            </p>
        </div>
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="btn nav-link {{ request()->routeIs('task.*') ? 'active' : '' }}"
                            href="{{ route('task.index') }}">Data-table Task</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn nav-link {{ request()->routeIs('ajax-crud') ? 'active' : '' }}"
                            href="{{ route('ajax-crud') }}">Ajax Task</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn nav-link" href="{{ route('profile.edit') }}">
                            {{ __('Profile') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" class="btn nav-link"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="mt-5">
            <div class="row">
                <div class="col-sm-2">
                    <ul class="nav nav-pills flex-column">
                        @if (request()->routeIs('task.*'))
                            <li class="nav-item">
                                <a class="nav-link disabled" href="#">Data-table Task</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('task.index') || request()->routeIs('task.edit') ? 'active' : '' }}"
                                    href="{{ route('task.index') }}">index</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('task.create') ? 'active' : '' }}"
                                    href="{{ route('task.create') }}">Create</a>
                            </li>
                        @elseif(request()->routeIs('ajax-crud'))
                            <li class="nav-item mb-1">
                                <a class="border border-primary btn btn-primary nav-link {{ request()->routeIs('ajax-crud') ? 'active' : '' }}"
                                    href="{{ route('ajax-crud') }}">Ajax Task</a>
                            </li>
                            <li class="nav-item mt-1">
                                <button type="button"
                                    class="nav-link border border-primary w-100 btn btn-primary create-task"
                                    data-url="{{ route('api-task.store') }}">
                                    Create
                                </button>
                            </li>
                        @endif
                    </ul>
                    <hr class="d-sm-none">
                </div>
                <div class="col-sm-10">
                    @yield('main-body')
                </div>
            </div>
        </div>

        <div class="mt-5 p-4 bg-dark text-white text-center">
            <p>Footer</p>
        </div>
    </div>
    @include('ajax.form-modal')
</body>


{{-- Js Files  --}}

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

</html>
```

### 3. Link the JavaScript File

Download the `ajax-jquery-crud.js` file and link it in your master layout (`app.blade.php`). This JavaScript file will handle all CRUD operations via AJAX and will be available throughout your project.

```js
<script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>
```

```js
    $(document).ready(function () {


        editSuccess = function (data) {

        }

        formSuccess = function (data) {

        }

        deleteSuccess = function () {
            setTimeout(() => {
                window.location.reload();
            }, 400); // 100ms delay
        }

        // Dynamic Ajax Call
        ajaxCall = function (param) {

            // Pre defining values
            var method = param.type;
            var url = param.url;
            var dataType = param.dataType;
            var data = param.data;
            var tostrTimeOut = 3000;

            // Call Ajax Function
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + $('meta[name="bearer-token"]').attr('content'), // Laravel bearer token,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },
                type: method,
                url: url,
                dataType: dataType,
                data: data,
                success: function (response) {
                    response.tostrTimeOut = tostrTimeOut; // adding time(sec) to response
                    success(response); // Call success callback
                }
            }).done(function (data, textStatus, jqXHR) {
                // Process data, After received in data parameter
                if (param.crud == 'edit') {
                    editSuccess(data);
                }
                else if (param.crud == 'formSubmit') {
                    formSuccess(data);
                }
                else if (param.crud == 'delete') {
                    deleteSuccess(data);
                }
                else { }
            }).fail(function ($xhr) {

                var errorData = $xhr.responseJSON; // Get Actual Json Response
                if (typeof errorData.message == "string") {
                    toastr.error('', errorData.message, { timeOut: tostrTimeOut }); // toastr Error Messages
                    // $('.error-message').append(`<span class="alert alert-danger mt-1 p-1">` + errorData.message + `</span>`); // Appending Error Messages
                }
                else {
                    $.each(errorData.message, function (objKey, objValue) { // Finding Each Data
                        toastr.error('', objValue, { timeOut: tostrTimeOut }); // toastr Error Messages
                        // $('.' + objKey).append(`<p class="alert alert-danger mt-1 p-1">` + objValue + `</p>`); // Appending Valiadtion Messages For Each Input
                    });
                }
                $('.alert').fadeOut(param.time); // Fading Away Error Message after time(Sec)
            });
        }

        // Close Modal
        closeModal = function () {
            // $('#form')[0].reset();
            $('.modal-title').html("Create Form"); // Replace Value
            $('#form').trigger('reset'); // Form Reset to empty
            $('#url').val(''); // url set empty
            $('input[name="_method"]').remove(); // remove input tag
            $("#form-modal").modal('hide'); // Hide Modal
        }

        // Close Modal on Click
        $(document).on('hidden.bs.modal', '#form-modal', function () {
            closeModal();
        });

        // Open Modal For Create or Edit data
        openModal = function (response) {

            // As, it is a single form using for both type file Submit Event.
            // so response.length defines if it has data object, then it's update form otherwise Create form
            if (typeof (response) !== "string" && Object.keys(response).length > 0) {
                $('.modal-title').html("Edit Form"); // Rename Modal Title
                let data = response.data;
                $.each(data, function (objKey, objValue) { // Finding/Assigning Each Data on Each Input
                    $('#' + objKey).find('option[value="${objValue}"]').attr('selected', 'selected').change();
                    $('#' + objKey).val(objValue); // Appending data for Each Input
                });

                $('#form').append('<input type="hidden" name="_method" value="PUT">'); // Needed Put method to Update Form
            }
            else {
                $('#url').val(response);
            }

            $("#form-modal").modal('show'); // Show Modal Form
        }

        // Show Modal On Click
        $(document).on('click', '.create-task', function () {
            url = $(this).attr('data-url');
            openModal(url);
        });

        // Edit JS
        $(document).ready(function () {
            // Edit/Show Data, Using Ajax
            $(document).on('click', '.edit-task', function () {
                var url = $(this).data('url'); // Get the delete URL
                var param = {
                    type: 'GET',
                    url: url,
                    dataType: 'JSON',
                }

                ajaxCall(param); // Submit form Using Ajax
            });

            // Form Submit for Create/Update Using Ajax
            $(document).on('click', '#formSubmitBtn', function (event) {
                event.preventDefault();

                var url = $('#url').val(); // Update/Create URL
                var method = $('input[name="_method"]').val(); // "POST" Method Create Form
                if (!method) {
                    method = $('#form').attr('method'); // "PUT" Method Update Form
                }
                var param = {
                    type: method,
                    url: url,
                    dataType: 'JSON',
                    data: $('#form').serialize(),
                    crud: 'formSubmit'
                }

                ajaxCall(param); // Submit form Using Ajax
            });
        });

        /*
        // Single delete data from table
        // Form Submit for Delete Using Ajax
        */
        $(document).on('click', '.delete-btn', function () {
            var id = $(this).data('id'); // Get the task ID
            var url = $(this).data('url'); // Get the delete URL
            if (confirm('Are you sure you want to delete this task?')) {
                var param = {
                    type: 'DELETE',
                    url: url,
                    dataType: 'JSON',
                    data: {
                        ids: id
                    }
                }
                ajaxCall(param); // Submit form Using Ajax
                $(this).closest('tr').remove();
            }
        });

        /*
        //  open multiple delete button
        */
        $(".checkitem").change(function () {
            if (this.checked) {
                $('#multiple_delete_btn').removeClass('d-none');
            }
            else if ($(".table input[name='id']:checked").length < 1) {
                $('#multiple_delete_btn').addClass('d-none');
                $('#check_all_box').prop('checked', false);
            }
        });

        // Check all boxes
        $('#check_all_box').click(function (event) {
            if (this.checked) {
                $('.checkitem').each(function () {
                    this.checked = true;
                    $('#multiple_delete_btn').removeClass('d-none');
                });
            } else {
                $('.checkitem').each(function () {
                    this.checked = false;
                    $('#multiple_delete_btn').addClass('d-none');
                });
            }
        });

        $('#multiple_delete_btn').on('click', function (e) {
            var url = $(this).data('url'); // Get the delete URL
            let selctedIds = [];
            $("input:checkbox[name=id]:checked").each(function () {
                selctedIds.push($(this).val());
            });

            if (confirm('Are you sure you want to delete this task?')) {
                var param = {
                    type: 'DELETE',
                    url: url,
                    dataType: 'JSON',
                    data: {
                        ids: selctedIds
                    },
                    crud: 'delete'
                }

                ajaxCall(param); // Submit form Using Ajax
                // deleteSwalAlert(selctedIds); // Calling Custom created Function
            }
        });
    });
```

## File Structure template/Alternatives

```
app/
resources/
├── views/
│   ├── ajax/
│   │   └── index.blade.php
│   └── master/
│       └── app.blade.php
public/
└── js/
    └── ajax-jquery-crud.js
```

## Key Files

### 1. `app.blade.php`

This is the main layout file that includes necessary CSS and JavaScript files.

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    @yield('custom-css')
</head>
<body>
    @yield('main-body')
    @include('ajax.form-modal')
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>
    @yield('custom-js')
</body>
</html>
```

### 2. `index.blade.php`

This file contains the table for displaying tasks and action buttons for CRUD operations.

```blade
    @extends('master.app')
    @section('main-body')
    <div class="container">
        <div class="card">
            <div class="card-header">Manage Tasks</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="task-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td>{{ $task->name }}</td>
                                <td>
                                    <button class="btn btn-primary edit" data-id="{{ $task->id }}">Edit</button>
                                    <button class="btn btn-danger delete" data-id="{{ $task->id }}">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button class="btn btn-success" id="add-task">Add Task</button>
                </div>
            </div>
        </div>
    </div>
    @endsection
```

### 3. `ajax-jquery-crud.js`

This JavaScript file handles AJAX requests for creating, updating, and deleting tasks.

```javascript 
    <script src="{{ asset('assets/js/ajax-jquery-crud-update.js') }}"></script>
```

### Key Changes Made:
- **Added Context**: Clear descriptions of what each function does.
- **Improved Comments**: More detailed comments explaining parameters and return values.
- **Standardized Structure**: Used consistent formatting for better readability.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.
