@extends('adminlte::page')

@section('title', 'Error 404')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3> 403 Error Page</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">403 Error Page</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="error-page">
        <h2 class="headline text-warning"> 403</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>

            <p>
                We could not find the page you were looking for.
            </p>
        </div>
        <!-- /.error-content -->
    </div>


@stop
<x-device-checker />
@section('plugins.Sweetalert2',true)

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{asset('js/clear_errors.js')}}"></script>
@stop
