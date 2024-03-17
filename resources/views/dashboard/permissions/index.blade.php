@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3 class="text-cyan"><i class="fa fa-check-circle"></i> Permissions</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        @can('add permission')
            <div class="card-header">
                <button class="btn btn-primary btn-sm" id="add-permission-btn">Add Permission</button>
            </div>
        @endcan
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                <table id="permission-list" class="table table-bordered table-hover" role="grid">
                    <thead>
                    <tr role="row">
                        <th>Date</th>
                        <th>Name</th>
                        <th>Roles</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th>Name</th>
                        <th>Roles</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @can('add permission')
        <!-- Modal -->
        <div class="modal fade permission-modal" id="new-permission" tabindex="-1" role="dialog" aria-labelledby="new-permission" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="add-permission-form">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 permission">
                                    <label for="permission">Permission</label>
                                    <input type="text" name="permission" class="form-control" id="permission">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-lg-12 role">
                                    <label for="role">Roles</label>
                                    <select name="role[]" class="form-control select2" id="role" multiple="multiple" data-placeholder="Select a role" style="width: 100%">
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->name}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@stop
@section('plugins.Sweetalert2',true)
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{asset('js/clear_errors.js')}}"></script>
    <script>
        $(function() {
            $('#permission-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('permission-list') !!}',
                columns: [
                    { data: 'updated_at', name: 'updated_at'},
                    { data: 'name', name: 'name'},
                    { data: 'roles', name: 'roles'},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive:true,
                order:[0,'desc'],
                pageLength: 20
            });
        });


        let permissionModal = $('.permission-modal');
        @can('add permission')
        $(document).on('click','#add-permission-btn', function(){
            permissionModal.modal('toggle');
            permissionModal.find('.modal-title').text('Add Permission');
            permissionModal.find('form').attr('id','add-permission-form')
            permissionModal.find('input[name=permission_id], .text-danger').remove();
            permissionModal.find('form').trigger('reset');
            permissionModal.find('#role').val([]).change();
        })

        $(document).on('submit','#add-permission-form', function(form){
            form.preventDefault();
            let data = $(this).serializeArray();

            $.ajax({
                url: '/permission',
                type: 'post',
                data: data,
                beforeSend: () => {
                    permissionModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                }
            }).done( (response, status, xhr) => {
                console.log(response);
                $('.text-danger').remove();
                if(response.success === true)
                {
                    Toast.fire({
                        type: "success",
                        title: response.message
                    });
                    permissionModal.find('form').trigger('reset');
                    $('#permission-list').DataTable().ajax.reload(null, false);
                }
            }).fail( (xhr, status, error) => {
                console.log(xhr);
                $.each(xhr.responseJSON.errors, function(key, value){
                    let element = $('.'+key);

                    element.find('.error-'+key).remove();
                    element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                });
            }).always( () => {
                permissionModal.find('button[type=submit]').attr('disabled',false).text('Save');
            });
            clear_errors('permission','role');
        });
        @endcan


        @can('edit permission')
        let permissionId;
        $(document).on('click','.edit-permission', function(){
            permissionId = this.id;

            permissionModal.find('input[name=permission_id]').remove();
            permissionModal.find('form').attr('id','edit-permission-form')
            permissionModal.modal('toggle');
            permissionModal.find('.modal-title').text('Edit Permission');
            permissionModal.find('.modal-body').append('<input type="hidden" name="permission_id" value="'+permissionId+'">');

            $.ajax({
                url: '/permission/'+permissionId+'/edit',
                type: 'get',
                beforeSend: function(){
                    permissionModal.find('#role, button[type=submit]').attr('disabled',true);
                }
            }).done(function(response){
                permissionModal.find('#permission').val(response.name);
                permissionModal.find('#role').val(response.roles).change();
                console.log(response)
            }).fail(function(xhr, status, error){
                console.log(xhr)
            }).always(function(){
                permissionModal.find('#role, #permission, button[type=submit]').attr('disabled',false);
            });
        });

        $(document).on('submit','#edit-permission-form', function(form){
            form.preventDefault();
            let data = $(this).serializeArray();

            $.ajax({
                url: '/permission/'+permissionId,
                type: 'put',
                data: data,
                beforeSend: () => {
                    permissionModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                }
            }).done( (response, status, xhr) => {
                console.log(response)
                $('.text-danger').remove();
                if(response.success == true)
                {
                    Toast.fire({
                        type: "success",
                        title: response.message
                    });
                    permissionModal.modal('toggle')
                    $('#permission-list').DataTable().ajax.reload(null, false);
                }
                else if(response.success === false)
                {
                    Toast.fire({
                        type: "warning",
                        title: response.message
                    });
                }
            }).fail( (xhr, status, error) => {
                console.log(xhr);
                $.each(xhr.responseJSON.errors, function(key, value){
                    let element = $('.'+key);

                    element.find('.error-'+key).remove();
                    element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                });
            }).always( () => {
                permissionModal.find('button[type=submit]').attr('disabled',false).text('Save');
            });
        })
        @endcan

        @can('delete permission')
        $(document).on('click','.delete-permission', function(){
            let id= this.id;

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function () {
                return $(this).text();
            }).get();

            Swal.fire({
                title: 'Delete '+data[1]+' permission?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        'url' : '/permission/'+id,
                        'type' : 'DELETE',
                        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){

                        },success: function(response){
                            if(response.success === true){
                                $('#permission-list').DataTable().ajax.reload(null, false);

                                Swal.fire(
                                    'Deleted!',
                                    'Permission has been deleted.',
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

        $('.select2').select2();
    </script>
@stop
