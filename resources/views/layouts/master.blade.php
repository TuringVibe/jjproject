<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Joson Project Management</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700;900&display=swap">
    <link rel="stylesheet" href="{{asset('lib/bootstrap-4.6.0-dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/regular.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/brands.min.css')}}">
    <link rel="stylesheet" href="{{asset('lib/fontawesome-5.15.3/css/solid.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/general.css')}}">
    @stack('head')
</head>
<body>
    <div class="d-flex flex-nowrap">
        @includeUnless(isset($basic), 'layouts.sidebar')
        <div class="d-flex flex-column flex-grow-1">
            @include('layouts.header')
            @yield('content')
        </div>
    </div>
    @include('layouts.footer')
    <script src="{{asset('lib/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('lib/bootstrap-4.6.0-dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('lib/sweetalert2-10.15.7/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('lib/jQuery-slimScroll-1.3.8/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('js/general.js')}}"></script>
    @stack('scripts')
    <script>
        $(document).ready(function() {
            $('.custom-file-input').on('change', (e) => {
                var fileName = e.target.files[0].name;
                $(e.target).next('.custom-file-label').html(fileName);
            });
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
