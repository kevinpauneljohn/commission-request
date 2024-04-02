@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>User Management</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">User Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-success card-outline card-tabs">
        <div class="card-body">
            @if(auth()->user()->can('add user'))
                <button class="btn btn-sm btn-success mb-4" id="new-backend-user-btn">Add New</button>
            @endif
            {{ $dataTable->table(['class' => 'table table-bordered table-hover','style' => 'width:100%;'], true) }}
        </div>
        <!-- /.card -->
    </div>

    <!-- Modal -->
    <div class="modal fade user-modal" id="new-backend-user" tabindex="-1" role="dialog" aria-labelledby="new-backend-user" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="add-backend-user-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-4 firstname">
                                <label for="firstname">First Name</label><span class="required">*</span>
                                <input type="text" class="form-control" name="firstname" id="firstname" />
                            </div>
                            <div class="form-group col-lg-4 middlename">
                                <label for="middlename">Middle Name</label>
                                <input type="text" class="form-control" name="middlename" id="middlename" />
                            </div>
                            <div class="form-group col-lg-4 lastname">
                                <label for="lastname">Last Name</label><span class="required">*</span>
                                <input type="text" class="form-control" name="lastname" id="lastname" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 date_of_birth">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" />
                            </div>
                            <div class="form-group col-lg-6 mobile_number">
                                <label for="mobile_number">Mobile Number</label>
                                <input type="text" class="form-control" name="mobile_number" id="mobile_number" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 email">
                                <label for="email">Email</label><span class="required">*</span>
                                <input type="email" name="email" class="form-control" id="email">
                            </div>
                            <div class="form-group col-lg-6 username">
                                <label for="username">Username</label><span class="required">*</span>
                                <input type="text" name="username" class="form-control" id="username">
                            </div>
                        </div>
                        <div class="row credentials">
                            <div class="form-group col-lg-6 password">
                                <label for="password">Password</label><span class="required">*</span>
                                <input type="password" name="password" class="form-control" id="password" />
                            </div>
                            <div class="form-group col-lg-6 password_confirmation">
                                <label for="password_confirmation">Password</label><span class="required">*</span>
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 form-group role">
                                <label for="role">Roles</label>
                                <select class="form-control select2" name="role[]" multiple="multiple" data-placeholder="Select a role" id="role" style="width: 100%;">
                                    <option value="">-- Select Role --</option>
                                    @foreach($backendRoles as $role)
                                        <option value="{{$role->name}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
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
@stop
<x-device-checker />
@section('plugins.Sweetalert2',true)
@section('css')

@stop

@push('js')
    <script src="{{asset('js/clear_errors.js')}}"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-right",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        let backendModal = $('#new-backend-user');
        let backendUserTable = $('#new-backend-users-table')
        $(document).on('click','#new-backend-user-btn',function(){
            backendModal.modal('toggle');
            backendModal.find('.credentials').removeAttr('style');
            backendModal.find('#username').attr('disabled',false);
            backendModal.find('.modal-title').text('New Backend User');
            backendModal.find('form').attr('id','add-backend-user-form');
            backendModal.find('input[name=user_id], .text-danger').remove();
            $('#add-backend-user-form').trigger('reset');
            $('#add-backend-user-form').find('#role').val([]).change();
        })

        @can('add user')
        $(document).on('submit','#add-backend-user-form',function(form){
            form.preventDefault();
            let data = $(this).serializeArray();

            // console.log(data)

            $.ajax({
                url: '{{route('user.store')}}',
                type: 'POST',
                data: data,
                beforeSend: function (){
                    $('#add-backend-user-form').find('button[type=submit]').attr('disabled',true).text('Saving...');
                }
            }).done((result) => {
                console.log(result)
                if(result.success === true)
                {
                    Toast.fire({
                        icon: "success",
                        title: result.message
                    });
                    backendUserTable.DataTable().ajax.reload(null, false);
                    $('#add-backend-user-form').trigger('reset');
                    $('#add-backend-user-form').find('#role').val(null).change();
                }else{
                    Toast.fire({
                        icon: "danger",
                        title: result.message
                    });
                }
            }).fail((xhr, status, error) => {
                // console.log(xhr.responseJSON.errors)
                $.each(xhr.responseJSON.errors, function(key, value){
                    let element = $('.'+key);

                    element.find('.error-'+key).remove();
                    element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                });
            }).always(() => {
                $('#add-backend-user-form').find('button[type=submit]').attr('disabled',false).text('Save');
            });
            clear_errors('firstname','lastname','date_of_birth','mobile_number','email','username','password','role');
        });
        @endcan

        @can('edit user')
            let backEndUserId;
            $(document).on('click','.edit-backend-user', function(){
                backEndUserId = this.id;

                backendModal.find('.text-danger').remove();
                backendModal.find('#username').attr('disabled',true);
                backendModal.find('.modal-title').text('Edit Backend User')
                backendModal.find('form').attr('id','edit-backend-user-form')
                backendModal.find('input[name=backend_user_id]').remove();
                backendModal.find('.credentials').attr('style','display:none');
                backendModal.modal('toggle')

                $.ajax({
                    url: '/user/'+backEndUserId+'/edit',
                    type: 'get',
                    beforeSend: function(){
                        backendModal.find('.modal-body').append(`<input type="hidden" name="user_id" value="${backEndUserId}">`);
                    }
                }).done( (response) => {
                    // console.log(response)
                    backendModal.find('#firstname').val(response.firstname);
                    backendModal.find('#middlename').val(response.middlename);
                    backendModal.find('#lastname').val(response.lastname);
                    backendModal.find('#date_of_birth').val(response.date_of_birth);
                    backendModal.find('#mobile_number').val(response.mobile_number);
                    backendModal.find('#email').val(response.email);
                    backendModal.find('#username').val(response.username);
                    backendModal.find('#role').val(response.roles).change();
                }).fail( (xhr, status, error) => {
                   console.log(xhr)
                }).always( () => {

                });
            });

            $(document).on('submit','#edit-backend-user-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/user/'+backEndUserId,
                    type: 'PUT',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    beforeSend: function(){

                    }
                }).done( (response) => {
                    console.log(response);
                    if(response.success === true)
                    {

                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        backendUserTable.DataTable().ajax.reload(null, false);
                        backendModal.modal('toggle')
                    }else if(response.success === false)
                    {
                        Toast.fire({
                            icon: "warning",
                            title: response.message
                        });
                    }
                }).fail( (xhr,status, error) => {
                    console.log(xhr.responseJSON.errors)
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                });
                clear_errors('firstname','lastname','date_of_birth','mobile_number','email','username','role');
            })

        @endcan

            @can('delete user')

            $(document).on('click','.delete-backend-user', function(){
                let id= this.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            'url' : '/user/'+id,
                            'type' : 'DELETE',
                            'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                if(response.success === true){
                                    backendUserTable.DataTable().ajax.reload(null, false);

                                    Swal.fire(
                                        'Deleted!',
                                        'User has been deleted.',
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
    {{ $dataTable->scripts() }}
@endpush


