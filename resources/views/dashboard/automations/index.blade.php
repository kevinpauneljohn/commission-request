@extends('adminlte::page')

@section('title', 'Automations')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Automation</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">Automation</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-purple card-outline">
                <div class="card card-header">
                    <h3 class="card-title">Task Templates</h3>
                </div>
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="request-activities" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th></th>
                                <th>Activities</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-pink card-outline">
                <div class="card card-header">
                    <h3 class="card-title">Sequence</h3>
                </div>
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="request-activities" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th></th>
                                <th>Activities</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('plugins.Sweetalert2',true)
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
@stop
