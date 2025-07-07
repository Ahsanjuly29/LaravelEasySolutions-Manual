@extends('auth.app')
@section('main-body')
    <div class="p-2">
        <h2>Login form</h2>
        <p class="error-message"></p>
        <form id="loginForm" action="/action_page.php">
            <div class="mb-3 mt-3 email">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
            </div>
            <div class="mb-3 password">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
            </div>
            <button type="button" class="btn btn-success" id="submitBtn">Submit</button>
        </form>
    </div>

    {{-- // let form = $(this)[0];
    // let formData = new FormData(form); // Create FormData object from the form --}}
@endsection

@section('custom-js')
    <script type="text/javascript">
        {{-- <script type="module"> --}}
        $(document).ready(function() {
            $('#submitBtn').click(function(event) {
                event.preventDefault();

                var param = {
                    type: 'POST',
                    url: "{{ route('login.post') }}",
                    dataType: 'JSON',
                    data: $('#loginForm').serialize(),
                    time: 300000
                }
                ajaxCall(param); // Submit form Using Ajax
            });
        });






        function success(data) {

            location.replace = "{{ route('me') }}";

            ///////////////////////////////////////////////////////
            // Second AJAX call after receiving the login token //
            //////////////////////////////////////////////////////
            // $.ajax({
            //     headers: {
            //         'Authorization': 'Bearer ' + data.data.token,
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
            //     },
            //     type: "GET",
            //     url: "{{ route('me') }}",

            //     success: function(response) {
            //         if (response.status == 1) {
            //             // console.log(response);
            //             // console.log('Token set successfully');

            //             // // Redirect to the new page with data as query parameters
            //             // const queryParams = new URLSearchParams(response.data).toString();
            //             // window.location.href = '/me' + queryParams; // Adjust the route as needed

            //             window.location.href = '/me'; // Adjust the route as needed

            //         } else {
            //             console.log(response);
            //             // alert('Error: ' + response.message);
            //         }
            //     },
            //     error: function($xhr) {
            //         var errorData = $xhr.responseJSON;
            //         if (typeof errorData.message === "string") {
            //             $('.error-message').append(`<span class="alert alert-danger mt-1 p-1">` + errorData
            //                 .message + `</span>`);
            //         } else {
            //             $.each(errorData.message, function(objKey, objValue) {
            //                 $('.' + objKey).append(`<p class="alert alert-danger mt-1 p-1">` +
            //                     objValue + `</p>`);
            //             });
            //         }
            //         $('.alert').fadeOut(3000000);
            //     }
            // });
        }
    </script>
@endsection
