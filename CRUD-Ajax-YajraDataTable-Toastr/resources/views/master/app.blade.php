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
