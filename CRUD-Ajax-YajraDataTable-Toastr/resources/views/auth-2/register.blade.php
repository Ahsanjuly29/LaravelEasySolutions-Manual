@extends('auth.app')
@section('main-body')
    {{--  --}}
    <div class="p-2">
        <h1>Register</h1>
        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="container">
                <p>Please fill in this form to create an account.</p>
                <hr>
                <div class="mb-3 mt-3">
                    <label for="email"><b>Email</b></label>
                    <input class="form-control" type="text" placeholder="Enter Email" name="email" id="email" required>

                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                </div>
                <div class="mb-3 mt-3">
                    <label for="psw"><b>Password</b></label>
                    <input class="form-control" type="password" placeholder="Enter Password" name="psw" id="psw"
                        required>
                </div>
                <div class="mb-3 mt-3">
                    <label for="psw-repeat"><b>Repeat Password</b></label>
                    <input class="form-control" type="password" placeholder="Repeat Password" name="psw-repeat"
                        id="psw-repeat" required>
                </div>
                <button class="btn btn-success" type="submit" id="registerbtn">Register</button>
            </div>

            <div class="container signin">
                <hr>
                <p>Already have an account? <a href="{{ route('login') }}">Sign in</a>.</p>
            </div>
        </form>
    </div>
@endsection
