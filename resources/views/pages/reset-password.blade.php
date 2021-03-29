@extends('layouts.master')

@section('content')
    <div class="container d-flex flex-column justify-content-center">
        <div class="row justify-content-center">
            <div class="col-sm-6">
                @if(session('error') != null)
                    {{ session('error') }}
                @endif
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div class="card">
                    <h4 class="card-header">Reset Password</h4>
                    <div class="card-body">
                        <p class="card-text">Input your new password</p>
                        <form method="POST" action="">
                            @csrf
                            <input type="hidden" name="token" value="{{$token}}">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{$email}}" aria-describedby="validate-email" readonly aria-readonly="true">
                                @error('email')
                                    <div id="validate-email" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input autocomplete="off" type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" aria-describedby="validate-password">
                                @error('password')
                                    <div id="validate-password" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password-confirmation">Confirm Password</label>
                                <input autocomplete="off" type="password" class="form-control" id="password-confirmation" name="password_confirmation">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <div class="col-sm-6 my-3">
                                <button class="btn btn-default btn-block" type="button" onclick="$('form').submit()">Reset Password</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
