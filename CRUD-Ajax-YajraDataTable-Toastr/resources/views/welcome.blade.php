<!DOCTYPE html>
<html>

<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        * {
            font-family: "Figtree" !important
        }

        a {
            letter-spacing: 2px;
        }

        body {
            background: black;
            color: rgb(180, 180, 180);
        }

        h1 {
            color: rgb(228, 211, 178);
        }

        .title-list {
            list-style-type: none;
        }

        .title-list b {
            font-size: 20px;
            color: white;
        }
    </style>
</head>

<body>

    {{--
    @dd(Session::get('loginToken' . auth()->user()->id), session('loginToken' . auth()->user()->id)) --}}

    <!-- Navbar (sit on top) -->
    <div class="w3-top">
        @if (Route::has('login'))
            <div class="w3-bar w3-white w3-padding w3-card" style="letter-spacing:4px;">
                <a href="{{ route('/') }}" class="w3-bar-item w3-button">{{ env('APP_NAME') }}</a>
                <!-- Right-sided navbar links. Hide them on small screens -->
                <div class="w3-right w3-hide-small">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w3-bar-item w3-button">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="w3-bar-item w3-button">Login</a>
                        <a href="{{ route('register') }}" class="w3-bar-item w3-button">Register</a>

                    @endauth
                </div>
            </div>
        @endif
    </div>


    <!-- Page content -->
    <div class="w3-content" style="max-width:1100px">

        <!-- About Section -->
        <div class="w3-row w3-padding-64" id="about">
            <div class="w3-col m6 w3-padding-large w3-hide-small">
                <a href="https://laracasts.com/">
                    <img src="https://laracasts.com/images/path/twitter-card.jpg?v=12"
                        class="w3-round w3-image w3-opacity-min" alt="Table Setting" width="600" height="750">
                </a>
            </div>

            <div class="w3-col m6 w3-padding-large">
                <h1>Website Features: </h1>
                <div>
                    <li class="title-list">
                        <p class="w3-large">
                            <b>User Authentication:</b>
                            Users should be able to register, log in, and log out.
                        </p>
                    </li>
                    <li class="title-list">
                        <p class="w3-large">
                            <b>Task CRUD Operations:</b>
                        <ol>
                            <li>
                                <p class="w3-large">
                                    <b>Create:</b>
                                    Users are able to add new tasks.
                                </p>
                            </li>
                            <li>
                                <p class="w3-large">
                                    <b>Read:</b>
                                    Users are able to view a list of their tasks.
                                </p>
                            </li>
                            <li>
                                <p class="w3-large">
                                    <b>Update: </b>
                                    Users are able to edit existing tasks.
                                </p>
                            </li>
                            <li>
                                <p class="w3-large">
                                    <b>Delete:</b>
                                    Users are able to remove tasks.
                                </p>
                            </li>
                        </ol>
                        </p>
                    </li>
                    <li class="title-list">
                        <p class="w3-large">
                            <b>Task Filtering and Sorting:</b>
                        <ol>
                            <li>
                                <p>Filter tasks by status (e.g., Pending, In Progress, Completed).</p>
                            </li>
                            <li>
                                <p>Sort tasks by due date.</p>
                            </li>
                        </ol>
                        </p>
                    </li>
                </div>
            </div>
        </div>
        <hr>
        <!-- Menu Section -->
        <div class="w3-row w3-padding-64" id="menu">
            <div class="w3-col l6 w3-padding-large">
                <h1 class="w3-center">Api Documentation</h1><br>
                <ol>
                    <li>JSON View</li>
                    <li>Graphical View</li>
                </ol>
                <h4></h4>
            </div>

            <div class="w3-col l6 w3-padding-large">
                <h1>JSON View</h1><br>
                <iframe class="w3-round w3-image w3-opacity-min" src="http://127.0.0.1:8000/docs/api.json"
                    name="iframe_a" style="height:80vh; width:100%;"></iframe>
            </div>
        </div>

        <hr>

        <!-- Contact Section -->
        <div class="w3-container w3-padding-64" id="contact">
            <h1>Graphical View</h1><br>
            <iframe class="w3-round w3-image w3-opacity-min" src="http://127.0.0.1:8000/docs/api#/" name="iframe_a"
                style="height:80vh; width:100%;"></iframe>
        </div>

        <!-- End page content -->
    </div>

    <!-- Footer -->
    <footer class="w3-center w3-light-grey w3-padding-32">
        <p>Developed by
            <a href="https://github.com/Ahsanjuly29" title="Ahsan Github Link" target="_blank"
                class="w3-hover-text-green">
                Ahsan Ahmed
            </a>
        </p>
    </footer>
</body>

</html>
