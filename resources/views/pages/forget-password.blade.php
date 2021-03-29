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
                    <h4 class="card-header">Forget Password</h4>
                    <div class="card-body">
                        <p class="card-text">Input your valid email so we can send link to your email to reset your password</p>
                        <form method="POST" action="">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" aria-describedby="validate-email">
                                @error('email')
                                    <div id="validate-email" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <div class="col-sm-6 my-3">
                                <button class="btn btn-default btn-block" type="button" onclick="$('form').submit()">Send link</button>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <a href="{{route('login')}}">Back to login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
