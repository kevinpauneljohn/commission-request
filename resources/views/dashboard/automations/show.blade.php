@extends('adminlte::page')

@section('title', 'Automations')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>{{ucfirst($automation->title)}}</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item"><a href="{{route('automation.index')}}">Automation</a> </li>
                <li class="breadcrumb-item active">Automation #{{$automation->id}}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-success card-outline">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <button class="btn bg-success btn-sm" id="create-task-template-btn">Create Task Template</button>

                </div>
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="task-template-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                                <tr role="row">
                                    <th style="width: 5%"></th>
                                    <th style="width: 5%">Sequence</th>
                                    <th style="width: 20%">Title</th>
                                    <th style="width: 30%">Description</th>
                                    <th>Assigned To</th>
                                    <th>Created By</th>
                                    <th style="width: 5%">Days Ref.</th>
                                    <th style="width: 11%"></th>
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
                            <div class="row">
                                <div class="col-lg-12 mt-3 description">
                                    <label for="description">Description</label><span class="required">*</span> <i>(max of 5000 characters)</i>
                                    <textarea id="description" name="description" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mt-3 assign_to">
                                    <label for="assign_to">Assign to role</label><span class="required">*</span>
                                    <select class="form-select" id="assign_to" name="assign_to" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->name}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mt-3 days_before_due_date">
                                    <label for="days_before_due_date">Days Before Due Date</label> <i class="fa fa-exclamation-circle" data-toggle="tooltip" data-placement="top" title="Choose the number of days for the task's due date upon its creation."></i>
                                    <select name="days_before_due_date" id="days_before_due_date" class="form-control">
                                        <option value="">--Select Number of Days</option>
                                        @for($days = 1; $days <= 90; $days++)
                                            <option value="{{$days}}">
                                                @if($days <= 1) {{$days}} Day @else {{$days}} Days @endif
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="automation_id" value="{{$automation->id}}"/>
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
@section('plugins.SummerNote',true)
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{asset('js/clear_errors.js')}}"></script>
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

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        @if('view automation')
        $('#task-template-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('automation-task-list',['automation_id' => $automation->id]) !!}',
            columns: [
                { data: 'sequence_number', name: 'sequence_number'},
                { data: 'sequence_id', name: 'sequence_id'},
                { data: 'title', name: 'title'},
                { data: 'description', name: 'description'},
                { data: 'assigned_to_role', name: 'assigned_to_role'},
                { data: 'creator', name: 'creator'},
                { data: 'days_before_due_date', name: 'days_before_due_date'},
                { data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            responsive:true,
            order:[0,'asc'],
            pageLength: 10
        });
        @endif
    </script>

    @if('add automation')
        <script>
            let automationModal = $('.automation-modal');

            $(document).ready(function(){

                $.ajax({
                    url: 'https://api.github.com/emojis',
                    async: false
                }).then(function(data) {
                    window.emojis = Object.keys(data);
                    window.emojiUrls = data;
                });

                $("#description").summernote({
                    minHeight: 250,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['fullscreen','help']],
                    ],
                    // placeholder: 'type starting with : and any alphabet',
                    hint: {
                        match: /:([\-+\w]+)$/,
                        search: function (keyword, callback) {
                            callback($.grep(emojis, function (item) {
                                return item.indexOf(keyword)  === 0;
                            }));
                        },
                        template: function (item) {
                            var content = emojiUrls[item];
                            return '<img src="' + content + '" width="20" /> :' + item + ':';
                        },
                        content: function (item) {
                            var url = emojiUrls[item];
                            if (url) {
                                return $('<img />').attr('src', url).css('width', 20)[0];
                            }
                            return '';
                        }
                    }
                })

                $('#assign_to').select2({
                    dropdownParent: $('.automation-modal'),
                    placeholder: 'Select an assignee',
                    allowClear: true,
                });

            });

            $(document).on('click','#create-task-template-btn',function(){
                automationModal.modal('toggle')
                automationModal.find('.modal-title').text('Create Task Template')
                automationModal.find('form').attr('id','add-automation-form')
                automationModal.find('form').trigger('reset')
                automationModal.find('#description').summernote('code','')
                automationModal.find('#assign_to').val('').change()
            })

            $(document).on('submit','#add-automation-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/automation-task',
                    type: 'post',
                    data: data,
                    beforeSend: function(){
                        automationModal.find('input, select, button').attr('disabled',true)
                        automationModal.find('#description').summernote('disable')
                    }
                }).done(function(response){
                    console.log(response);
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#task-template-list').DataTable().ajax.reload(null, false);
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
                    automationModal.find('input, select, button').attr('disabled',false)
                    automationModal.find('#description').summernote('enable')
                })
                clear_errors('title','description','assign_to','days_before_due_date')
            });
        </script>
    @endif

    @if('edit automation')
        <script>
            let automationId;
            $(document).on('click','.edit-automation-btn',function(){
                automationId = this.id;
                automationModal.modal('toggle')
                automationModal.find('.modal-title').text('Edit Task Template')
                automationModal.find('form').attr('id','edit-automation-form')

                $.ajax({
                    url: '/automation-task/'+automationId+'/edit',
                    type: 'get',
                    beforeSend: function(){
                        automationModal.find('input, select,button[type=submit]').attr('disable',true);
                        automationModal.find('#description').summernote('disable');
                    }
                }).done(function(response){
                    // console.log(response)
                    automationModal.find('input[name=title]').val(response.title)
                    automationModal.find('#description').summernote('code',response.description)
                    automationModal.find('#assign_to').val(response.assigned_to_role).change()
                    automationModal.find('#days_before_due_date').val(response.days_before_due_date).change()
                }).fail(function(xhr, status, error){

                }).always(function(){
                    automationModal.find('input, select,button[type=submit]').attr('disable',false);
                    automationModal.find('#description').summernote('enable');
                })
            })

            $(document).on('submit','#edit-automation-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/automation-task/'+automationId,
                    type: 'put',
                    data: data,
                    beforeSend: function(){
                        automationModal.find('input, select, button').attr('disabled',true)
                        automationModal.find('#description').summernote('disable')
                    }
                }).done(function(response){
                    console.log(response);
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#task-template-list').DataTable().ajax.reload(null, false);
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
                    automationModal.find('input, select, button').attr('disabled',false)
                    automationModal.find('#description').summernote('enable')
                })
                clear_errors('title','description','assign_to','days_before_due_date')
            })

            $(document).on('change','.sequence',function(){
                let id = this.id;
                let sequenceValue = $(this).val();

                Swal.fire({
                    title: 'Update Sequence?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            'url' : '/change-sequence/{{$automation->id}}/'+id+'/'+sequenceValue,
                            'type' : 'put',
                            'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                console.log(response)
                                if(response.success === true){

                                    Swal.fire(
                                        'Updated!',
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
                                $('#task-template-list').DataTable().ajax.reload(null, false);
                            },error: function(xhr, status, error){
                                console.log(xhr);
                            }
                        });

                    }else{
                        $('#task-template-list').DataTable().ajax.reload(null, false);
                    }

                });
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
                    title: 'Delete Task: '+data[0]+'?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            'url' : '/automation-task/'+id,
                            'type' : 'DELETE',
                            'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                if(response.success === true){
                                    $('#task-template-list').DataTable().ajax.reload(null, false);

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
