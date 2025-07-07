<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    {{-- Css Files  --}}
    {{-- @vite(['assets/css/app.css', 'assets/css/bootstrap.css', 'assets/css/bootstrap.min.css']) --}}

    @yield('custom-css')
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-sm-8 offset-sm-2 border border-rounded bg-light ">
                @yield('main-body')
            </div>
        </div>
    </div>

    {{-- Js Files  --}}
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>
    @yield('custom-js')


    {{-- Js Files  --}}
    {{-- @vite(['resources/assets/js/app.js', 'resources/assets/js/bootstrap.js', 'resources/assets/js/bootstrap.min.js', 'resources/assets/js/jquery.min.js']) --}}

</body>

</html>
