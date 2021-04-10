@extends('layouts.master')

@section('content')
    <div class="container d-flex flex-column justify-content-center">
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div class="card">
                    <h4 class="card-header">Login</h4>
                    <div class="card-body">
                        <form method="POST" action="">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember-me" name="remember_me">
                                <label for="remember-me" class="form-check-label">Remember me</label>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <div class="col-sm-6 my-3">
                                <button class="btn btn-default btn-block" type="button" onclick="$('form').submit()">Login</button>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <a href="{{route('password.forget')}}">Forget Password</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ready-scripts')
    $('input.form-control').on('keyup',(e) => {
        if(e.key === "Enter") $('form').submit();
    });
@endpush
