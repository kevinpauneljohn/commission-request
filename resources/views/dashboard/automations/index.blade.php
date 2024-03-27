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
        <div class="col-lg-12">
            <div class="card card-success card-outline">
                @if('add automation')
                    <div class="card-header">
                        <button class="btn bg-success btn-sm" id="create-task-template-btn">Create</button>
                    </div>
                @endif
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="automation-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th style="width: 15%;">Date Created</th>
                                <th style="width: 10%;">Automation ID</th>
                                <th>Title</th>
                                <th style="width: 15%;">Creator</th>
                                <th style="width: 5%;">Active</th>
                                <th style="width: 11%;"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('add automation')
        <!-- Modal -->
        <div class="modal fade automation-modal" id="new-automation" tabindex="-1" role="dialog" aria-labelledby="action-taken" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form id="add-automation-form">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 title">
                                    <label for="title">Title</label><span class="required">*</span> <i>(max of 250 characters)</i>
                                    <input type="text" id="title" name="title" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan

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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()

            @if('view automation')
                $('#automation-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('automation-lists') !!}',
                    columns: [
                        { data: 'created_at', name: 'created_at'},
                        { data: 'id', name: 'id'},
                        { data: 'title', name: 'title'},
                        { data: 'user_id', name: 'user_id'},
                        { data: 'is_active', name: 'is_active'},
                        { data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive:true,
                    order:[1,'asc'],
                    pageLength: 50
                });
            @endif
        })
    </script>
    @if('add automation')
        <script>
            let automationModal = $('.automation-modal');


            $(document).on('click','#create-task-template-btn',function(){
                automationModal.modal('toggle')
                automationModal.find('.modal-title').text('Create Automation')
                automationModal.find('form').attr('id','add-automation-form')
                automationModal.find('form').trigger('reset')
                automationModal.find('.text-danger').remove()
            })

            $(document).on('submit','#add-automation-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/automation',
                    type: 'post',
                    data: data,
                    beforeSend: function(){
                        automationModal.find('input, button').attr('disabled',true);
                        automationModal.find('button[type=submit]').text('Saving...');
                    }
                }).done(function(response){
                    console.log(response);
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#automation-list').DataTable().ajax.reload(null, false);
                        automationModal.find('form').trigger('reset')
                        automationModal.modal('toggle')
                    }else if(response.success === false)
                    {
                        Toast.fire({
                            icon: "warning",
                            title: response.message
                        });
                    }
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    automationModal.find('input, button').attr('disabled',false);
                    automationModal.find('button[type=submit]').text('Save');
                })
                clear_errors('title')
            });
        </script>
    @endif

    @if('edit automation')
        <script>
            let automationId;
            $(document).on('click','.edit-automation-btn',function(){
                automationId = this.id;
                automationModal.modal('toggle')
                automationModal.find('.modal-title').text('Edit Automation')
                automationModal.find('form').attr('id','edit-automation-form')
                automationModal.find('.text-danger').remove()

                $.ajax({
                    url: '/automation/'+automationId+'/edit',
                    type: 'get',
                    beforeSend: function(){
                        automationModal.find('input, button').attr('disabled',true);
                    }
                }).done(function(response){
                    console.log(response)
                    automationModal.find('input[name=title]').val(response.title)
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                }).always(function(){
                    automationModal.find('input, button').attr('disabled',false);
                })
            })

            $(document).on('submit','#edit-automation-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/automation/'+automationId,
                    type: 'put',
                    data: data,
                    beforeSend: function(){
                        automationModal.find('input, button').attr('disabled',true);
                        automationModal.find('button[type=submit]').text('Saving...');
                    }
                }).done(function(response){
                    console.log(response);
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#automation-list').DataTable().ajax.reload(null, false);
                        automationModal.modal('toggle')
                    }else if(response.success === false)
                    {
                        Toast.fire({
                            icon: "warning",
                            title: response.message
                        });
                    }
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    automationModal.find('input, button').attr('disabled',false);
                    automationModal.find('button[type=submit]').text('Save');
                })
                clear_errors('title')
            });
        </script>
    @endif

    @if(auth()->user()->can('delete automation'))
        <script>
            $(document).on('click','.delete-automation-btn', function(){
                let id= this.id;

                $tr = $(this).closest('tr');

                let data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();

                Swal.fire({
                    title: 'Delete Automation #'+data[1]+'?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            'url' : '/automation/'+id,
                            'type' : 'DELETE',
                            'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                if(response.success === true){
                                    $('#automation-list').DataTable().ajax.reload(null, false);

                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                }else{
                                    Swal.fire(
                                        'Warning!',
                                        response.message,
                                        'warning'
                                    );
                                }
                            },error: function(xhr, status, error){
                                console.log(xhr);
                            }
                        });

                    }
                });
            });
        </script>
    @endif
@stop
