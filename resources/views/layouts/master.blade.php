<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Joson Project Management</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700;900&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/regular.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/brands.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/solid.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/general.css')}}">
    @stack('head')
</head>
<body>
    @include('layouts.header')
    <div class="d-flex">
        @includeUnless(isset($basic), 'layouts.sidebar')
        @yield('content')
    </div>
    @include('layouts.footer')
    <script src="{{asset('lib/jquery-3.6.0.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="{{asset('lib/sweetalert2-10.15.7/sweetalert2.all.min.js')}}"></script>
    @stack('scripts')
    <script>
        $(document).ready(function() {
            @stack('ready-scripts')
        });
    </script>
    <script>
        var sessionSuccess = @json(session('success')),
            sessionError = @json(session('error'));
        if(sessionSuccess !== null) {
            Swal.fire({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                title: 'Success',
                text: sessionSuccess,
                icon: 'success'
            });
        } else if(sessionError !== null) {
            Swal.fire({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                title: 'Error',
                text: sessionError,
                icon: 'error'
            });
        }
    </script>
</body>
</html>
