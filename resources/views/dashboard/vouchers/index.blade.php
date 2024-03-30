@extends('adminlte::page')

@section('title', 'Commission Vouchers')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3> Commission Vouchers</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item"><a href="{{route('request.index')}}">Requests</a> </li>
                <li class="breadcrumb-item active">Commission Vouchers</li>
            </ol>
        </div>
    </div>
@stop

@section('content')


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
