<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Tagify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />

    <style>
        .image-hover-wrapper {
            position: relative;
            display: inline-block;
        }

        .image-hover-popup {
            display: none;
            position: absolute;
            top: 0;
            left: 60px;
            background: white;
            border: 1px solid #ccc;
            padding: 5px;
            z-index: 999;
            white-space: nowrap;
        }

        .image-hover-wrapper:hover .image-hover-popup {
            display: block;
        }

        .blog-thumb {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .blog-hover-thumb {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .user-thumb {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .blog-main-image {
            max-height: 400px;
            object-fit: contain;
            border-bottom: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .image-hover-wrapper:hover .image-hover-popup {
                display: none !important;
            }
        }
    </style>



</head>

<body>

    @if (session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
            <div id="toast-success" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
            <div id="toast-error" class="toast align-items-center text-white bg-danger border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('blogs.index') }}">Blogs</a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link">Hello! {{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-4">
        @yield('content')
    </div>
    <div id="dynamic-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055"></div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastSuccess = document.getElementById('toast-success');
            var toastError = document.getElementById('toast-error');
            if (toastSuccess) {
                new bootstrap.Toast(toastSuccess).show();
            }
            if (toastError) {
                new bootstrap.Toast(toastError).show();
            }
        });

        function showToast(message, type = 'success') {
            let bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
            let toastId = 'toast-' + Date.now();
            let toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            $('#dynamic-toast-container').append(toastHtml);
            new bootstrap.Toast(document.getElementById(toastId)).show();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>



</body>

</html>