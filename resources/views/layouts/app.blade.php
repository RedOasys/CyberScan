<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MalSys') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<div id="app" class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 280px; min-height: 100vh;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="max-width: 100%; height: auto;">
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}" id="homebutton" aria-current="page">Home</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('upload') }}" class="nav-link {{ Request::is('upload*') ? 'active' : '' }}" id="upload">Upload</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('analysis') }}" class="nav-link {{ Request::is('analysis*') ? 'active' : '' }}" id="analysis">Analyze</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('files') }}" class="nav-link {{ Request::is('files*') ? 'active' : '' }}" id="files">Files</a>
            </li>

            <!-- Add more sidebar items with similar structure -->
        </ul>
        <hr>
        <div class="dropdown">
            @auth
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('noimageuser.png') }}" alt="" width="32" height="32" class="rounded-circle me-2">
                    {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                Sign out
                            </button>
                        </form>
                    </li>
                </ul>
            @else
                <!-- Display a link to the login page for guests -->
                <a href="{{ route('login') }}" class="text-white text-decoration-none">Login</a>
            @endauth
        </div>
    </div>
    <div class="b-example-divider"></div>
    <div class="p-3 w-100">
        <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
            <h2>@yield('title', 'Dashboard')</h2>
        </header>
        @yield('content')
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
<script>
    // Use jQuery to add the 'active' class to the currently active navigation link
    $(document).ready(function () {
        // Get the current URL path
        var currentPath = window.location.pathname;

        // Loop through each navigation link and compare its href attribute to the current path
        $(".nav-link").each(function () {
            var linkPath = $(this).attr("href");
            if (currentPath.startsWith(linkPath)) {
                $(this).addClass("active");
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>
