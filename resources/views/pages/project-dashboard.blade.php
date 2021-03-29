@extends('layouts.master')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header')
    </div>
@endsection
