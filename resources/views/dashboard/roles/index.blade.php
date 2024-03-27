@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Roles</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        @can('add role')
            <div class="card-header">
                <button class="btn btn-success btn-sm" id="add-role-btn">Add Role</button>
            </div>
        @endcan
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                <table id="role-list" class="table table-bordered table-hover" role="grid" style="width: 100%;">
                    <thead>
                    <tr role="row">
                        <th>Date</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th>Name</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @can('add role')
        <!-- Modal -->
        <div class="modal fade role-modal" id="new-role" tabindex="-1" role="dialog" aria-labelledby="new-role" aria-hidden="true">
            <div class="modal-dialog modal-xs" role="document">
                <form id="add-role-form">
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
                                <div class="col-lg-12 role">
                                    <label for="role">Role</label>
                                    <input type="text" name="role" class="form-control" id="role">
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
    <script>
        $(function() {
            $('#role-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('role-lists') !!}',
                columns: [
                    { data: 'updated_at', name: 'updated_at'},
                    { data: 'name', name: 'name'},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive:true,
                order:[0,'desc'],
                pageLength: 20
            });
        });


        let roleModal = $('.role-modal');
        @can('add role')
            $(document).on('click','#add-role-btn', function(){
                roleModal.modal('toggle');
                roleModal.find('.modal-title').text('Add Role');
                roleModal.find('form').attr('id','add-role-form')
                roleModal.find('input[name=role_id]').remove();
            })

            $(document).on('submit','#add-role-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                console.log(data)

                $.ajax({
                    url: '/role',
                    type: 'post',
                    data: data,
                    beforeSend: () => {
                        roleModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                    }
                }).done( (response, status, xhr) => {
                    // console.log(response);
                    $('.text-danger').remove();
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        roleModal.find('form').trigger('reset');
                        $('#role-list').DataTable().ajax.reload(null, false);
                    }
                }).fail( (xhr, status, error) => {
                    // console.log(xhr.responseJSON.errors);
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always( () => {
                    roleModal.find('button[type=submit]').attr('disabled',false).text('Save');
                });
            });
        @endcan


        @can('edit role')
            let editRoleId;
            $(document).on('click','.edit-role', function(){
                editRoleId = this.id;

                roleModal.find('input[name=role_id]').remove();
                roleModal.find('form').attr('id','edit-role-form')
                roleModal.modal('toggle');
                roleModal.find('.modal-title').text('Edit Role');
                roleModal.find('.modal-body').append('<input type="hidden" name="role_id" value="'+editRoleId+'">');

                $.ajax({
                    url: '/role/'+editRoleId+'/edit',
                    type: 'get',
                    beforeSend: function(){
                        roleModal.find('#role, button[type=submit]').attr('disabled',true);
                    }
                }).done(function(response){
                    roleModal.find('#role').val(response.name);
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                }).always(function(){
                    roleModal.find('#role, button[type=submit]').attr('disabled',false);
                });
            });

            $(document).on('submit','#edit-role-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/role/'+editRoleId,
                    type: 'put',
                    data: data,
                    beforeSend: () => {
                        roleModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                    }
                }).done( (response, status, xhr) => {
                    $('.text-danger').remove();
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        roleModal.find('form').trigger('reset');
                        roleModal.modal('toggle')
                        $('#role-list').DataTable().ajax.reload(null, false);
                    }
                    else if(response.success === false)
                    {
                        Toast.fire({
                            icon: "warning",
                            title: response.message
                        });
                    }
                }).fail( (xhr, status, error) => {
                    // console.log(xhr.responseJSON.errors);
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always( () => {
                    roleModal.find('button[type=submit]').attr('disabled',false).text('Save');
                });
            })
        @endcan

        @can('delete role')
        $(document).on('click','.delete-role', function(){
            let id= this.id;

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function () {
                return $(this).text();
            }).get();

            Swal.fire({
                title: 'Delete '+data[1]+' role?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        'url' : '/role/'+id,
                        'type' : 'DELETE',
                        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){

                        },success: function(response){
                            if(response.success === true){
                                $('#role-list').DataTable().ajax.reload(null, false);

                                Swal.fire(
                                    'Deleted!',
                                    'Role has been deleted.',
                                    'success'
                                );
                            }
                        },error: function(xhr, status, error){
                            console.log(xhr);
                        }
                    });

                }
            });
        });
        @endcan
    </script>
@stop
