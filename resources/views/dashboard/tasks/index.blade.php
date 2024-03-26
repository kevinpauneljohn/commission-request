@extends('adminlte::page')

@section('title', 'Tasks')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Tasks</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">Tasks</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <x-request.info-box />
    <div class="card card-success card-outline">
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <table id="request-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                    <thead>
                    <tr role="row">
                        <th style="width: 10%;">Request #</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
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


    <script>
        $('.select2, #sales_director').select2();
    </script>

@stop
