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
    <div class="card card-success card-outline">
        <div class="card-header">
            <div class="float-left mt-3" style="width: 50%;">
                <x-task.display />
            </div>
            <div class="float-right" >
                <button type="button" class="btn btn-flat mt-2" style="background-color: #effdfb;">Near Due Date</button>
                <button type="button" class="btn btn-flat mt-2" style="background-color: #fbf2cf;">Tomorrow</button>
                <button type="button" class="btn btn-flat mt-2" style="background-color: #fde5e1;">Today & Past Due</button>
            </div>
        </div>
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <table id="task-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                    <thead>
                    <tr role="row">
                        <th>Task #</th>
                        <th>Request #</th>
                        <th style="width: 30%">Title</th>
                        <th>Assigned to</th>
                        <th>Created By</th>
                        <th>Date Created</th>
                        <th>Due Date</th>
                        <th>Past Due</th>
                        <th>Status</th>
                        <th>Action Taken</th>
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
    @once
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-right",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
        </script>
    @endonce
    @can('view task')
        <script>
            $(function (){
                $('#task-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('task-list') !!}' ,
                    columns: [
                        { data: 'id', name: 'id'},
                        { data: 'request_id', name: 'request_id'},
                        { data: 'title', name: 'title'},
                        { data: 'assigned_to', name: 'assigned_to'},
                        { data: 'creator', name: 'creator'},
                        { data: 'created_at', name: 'created_at'},
                        { data: 'due_date', name: 'due_date'},
                        { data: 'past_due', name: 'past_due'},
                        { data: 'status', name: 'status'},
                        { data: 'action_taken', name: 'action_taken'},
                        { data: 'task_action', name: 'task_action', orderable: false, searchable: false}
                    ],
                    responsive:true,
                    order:[0,'desc'],
                    pageLength: 10,
                });
            });
        </script>
    @endcan

    <script>
        $('.select2, #sales_director').select2();
    </script>

@stop
