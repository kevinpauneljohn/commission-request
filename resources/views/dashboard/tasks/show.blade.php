@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Task Details</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item"><a href="{{route('request.show',['request' => $task->request_id])}}">Request #{{$task->formatted_request_id}}</a> </li>
                <li class="breadcrumb-item active">Task Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="card card-success card-outline">
                <div class="card-body">
                    <strong><i class="fas fa-ticket-alt"></i> From Request # <span class="text-primary text-bold"><a href="{{route('request.show',['request' => $task->request_id])}}">{{$task->request->formatted_id}}</a> </span></strong>
                    <hr>
                    <strong><i class="fas fa-user mr-1"></i> Assigned To</strong>

                    <p class="text-muted">
                        {{ucwords($task->assignedTo->full_name)}}
                    </p>

                    <hr>

                    <strong><i class="fas fa-user mr-1"></i> Creator</strong>

                    <p class="text-muted">
                        {{ucwords($task->author->full_name)}}
                    </p>

                    <hr>

                    <strong><i class="fas fa-calendar-check mr-1"></i> Date Created</strong>

                    <p class="text-info">
                        {{$task->created_at->format('M d, Y g:i:s a')}}
                    </p>
                    <hr>
                    <strong><i class="fas fa-calendar-check mr-1"></i> Due Date</strong>

                    <p class="text-danger">
                        {{\Carbon\Carbon::parse($task->due_date)->format('M d, Y')}}
                    </p>
                    <hr>
                    <strong><i class="fa fa-tags mr-1"></i> Status</strong>
                    <form id="status-form">
                        <label for="status"></label>
                        <select class="form-select form-control mt-3" id="status" name="status">
                            <option value="pending" @if($task->status == "pending") selected @endif>Pending</option>
                            <option value="on-going" @if($task->status == "on-going") selected @endif>On-going</option>
                            <option value="completed" @if($task->status == "completed") selected @endif>Completed</option>
                        </select>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card card-success card-outline ">
                <div class="card-header">
                    <h3 class="card-title text-bold">{{ucfirst($task->title)}}</h3>
                </div>
                <div class="card-body">
                    {!! $task->description !!}
                </div>
            </div>
            <div class="card card-success card-outline task-card">

                    @if($task->assigned_to == auth()->user()->id && auth()->user()->can('add action taken') || auth()->user()->can('edit action taken'))
                        @if($task->status != 'completed' && $task->request->status != 'completed' && $task->request->status != 'delivered')
                            <div class="card-header">

                                @if($task->assigned_to != auth()->user()->id)
                                    <h3 class="card-title text-danger">This task was not assigned to you</h3>
                                @else
                                    <button class="btn btn-primary btn-sm mb-1" id="action-taken">Add Action</button>
                                    <button type="button" class="btn btn-warning btn-sm mb-1" id="add-findings">Add Findings</button>
                                    @if(!$task->is_end)
                                        <button type="button" class="btn btn-success btn-sm mb-1" id="proceed-to-next-task">Proceed to next task</button>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endif

                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <table id="action-taken-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th style="width: 15%;">Date Created</th>
                                <th>Action Taken</th>
                                <th style="width: 15%;">Created By</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('add action taken')
        <!-- Modal -->
        <div class="modal fade action-taken-modal" id="new-action-taken" tabindex="-1" role="dialog" aria-labelledby="action-taken" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form id="action-taken-form">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="col-lg-12 mt-2 description">
                                <label for="description">Description</label><span class="required">*</span> <i>(max of 1500 characters)</i>
                                <textarea id="description" name="description" class="form-control" maxlength="1500"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="task_id" value="{{$task->id}}">
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

@stop

@push('js')
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
    </script>

    @if(auth()->user()->can('update task status'))
        <script>
            let status;
            let taskId = {{$task->id}};


            $(document).ready(function(){
                 status = $('#status').val();
            });


            $(document).on('change','#status', async function (form) {
                form.preventDefault();

                status = $(this).val()

                Swal.fire({
                    title: `Update status to ${status}?`,
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '/update-task-status',
                            type: 'put',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {task:taskId, status: status},
                            beforeSend: function () {
                                $('#status').attr('disabled', true)
                            }
                        }).done(function (response) {
                            console.log(response)
                            if(response.success === true)
                            {
                                Toast.fire({
                                    icon: "success",
                                    title: response.message
                                });
                                setTimeout(function(){
                                    location.reload();
                                },2000);
                            }else{
                                Swal.fire({
                                    title: "Warning!",
                                    text: response.message,
                                    icon: "warning"
                                })
                                $('#status-form').trigger('reset');
                            }
                        }).fail(function (xhr, status, error) {
                            console.log(xhr)
                        }).always(function () {
                            $('#status').attr('disabled', false)
                        });
                    }else{
                        $('#status-form').trigger('reset');
                    }
                });

            })
        </script>
    @endif

    @can('view task')
        <script>
            $('#action-taken-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('task-action-taken',['task' => $task->id]) !!}',
                columns: [
                    { data: 'created_at', name: 'created_at'},
                    { data: 'action_taken', name: 'action_taken'},
                    { data: 'user_id', name: 'user_id'},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive:true,
                order:[0,'desc'],
                pageLength: 50
            });
        </script>
    @endcan

    @if($task->status == "on-going" || $task->status == "completed"
        && auth()->user()->can('add action taken') || auth()->user()->can('edit action taken') && $task->assigned_to == auth()->user()->id
        || auth()->user()->hasRole('super admin'))
        <script>
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
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
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
                },
                callbacks: {
                    onChange: function(contents) {
                        if (contents == '<p><br></p>') {
                            var currentSummernoteInstance = $(this);
                            currentSummernoteInstance.summernote('code', '');
                        }
                    }
                }
            });





            let actionTakenModal = $('.action-taken-modal');


            $(document).on('submit','#action-taken-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                console.log(data);
                $.ajax({
                    url: '/action-taken',
                    type: 'post',
                    data: data,
                    beforeSend: function(){
                        actionTakenModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                    }
                }).done(function(response){
                    // console.log(response)

                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#action-taken-list').DataTable().ajax.reload(null, false);
                        $('#action-taken-form').trigger('reset');
                        actionTakenModal.modal('toggle');
                    }
                    else{
                        Toast.fire({
                            icon: "warning",
                            title: response.message
                        });
                    }

                }).fail(function(xhr, status, error){
                    console.log(xhr)
                    if(xhr.status === 403)
                    {
                        Toast.fire({
                            icon: "warning",
                            title: xhr.responseJSON.message
                        });
                    }
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    actionTakenModal.find('button[type=submit]').attr('disabled',false).text('Save');
                });
                clear_errors('description');
            });

            $(document).on('click','#action-taken',function(){
                actionTakenModal.find('.modal-title').text('Add Action Taken');
                actionTakenModal.modal('toggle');
                actionTakenModal.find('.text-danger').remove();
                actionTakenModal.find('form').attr('id','action-taken-form');
                actionTakenModal.find('#description').summernote('reset');
            })

            let actionTakenId;

            $(document).on('click','.edit-action-taken-btn',function(){
                actionTakenModal.find('.modal-title').text('Edit Action Taken');
                actionTakenModal.modal('toggle');
                actionTakenModal.find('.text-danger').remove();
                actionTakenModal.find('form').attr('id','edit-action-taken-form');

                actionTakenId = this.id;

                $.ajax({
                    url: '/action-taken/'+actionTakenId+'/edit',
                    type: 'get',
                    beforeSend: function(){

                    }
                }).done(function(response){
                    console.log(response)
                    actionTakenModal.find('#description').summernote("code",response.action)
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                });
            })


            $(document).on('submit','#edit-action-taken-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/action-taken/'+actionTakenId,
                    type: 'put',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    beforeSend: function(){
                        actionTakenModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                    }
                }).done(function(response){
                    console.log(response)

                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#action-taken-list').DataTable().ajax.reload(null, false);
                        actionTakenModal.modal('toggle');
                    }
                    else{
                        Toast.fire({
                            icon: "warning",
                            title: response.message
                        });
                    }

                }).fail(function(xhr, status, error){
                    console.log(xhr)
                    if(xhr.status === 403)
                    {
                        Toast.fire({
                            icon: "warning",
                            title: xhr.responseJSON.message
                        });
                    }
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    actionTakenModal.find('button[type=submit]').attr('disabled',false).text('Save');
                });
                clear_errors('description');
            });


            $(document).on('click','.delete-action-taken-btn', function(){
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
                            'url' : '/action-taken/'+id,
                            'type' : 'DELETE',
                            'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                if(response.success === true){
                                    $('#action-taken-list').DataTable().ajax.reload(null, false);

                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
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
        </script>

        <script>
            $(document).on('click','#add-findings', function(){
                actionTakenModal.modal('toggle');
                actionTakenModal.find('.modal-title').text('Add Findings');
                actionTakenModal.find('form').attr('id','add-findings-form');
                actionTakenModal.find('#description').summernote('reset');
                actionTakenModal.find('.text-danger').remove();
            });

            $(document).on('submit','#add-findings-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/create-findings',
                    type: 'post',
                    data: data,
                    beforeSend: function(){
                        actionTakenModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                    }
                }).done(function(response){
                    console.log(response)
                    if(response.success === true)
                    {
                        actionTakenModal.modal('toggle')
                        $('#action-taken-list').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            title: response.message,
                            text: 'Do you want to create a child request?',
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                        }).then((result) => {
                                                       if (result.value) {

                                $.ajax({
                                    url: '/request-declined/{{$task->request_id}}',
                                    type: 'put',
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    async: false,
                                }).then(function(data){
                                    if(data.success === true)
                                    {
                                        Swal.fire({
                                            title: "Confirmed",
                                            text: "Redirecting now to form page...",
                                            icon: "success",
                                            showConfirmButton: false,
                                        });

                                        setTimeout(function(){
                                            window.location.replace('{{route('request.index')}}?parent_request={{$task->request_id}}')
                                        },2000)
                                    }
                                })
                            }

                            if(result.dismiss)
                            {
                                let url = window.location.href;
                                $('#status-form').find('select').load(url+' #status-form select option');
                                $('.task-card').find('.card-header').remove()
                            }
                        });
                    }
                    else{

                        Swal.fire(
                            response.message,
                            '',
                            'warning'
                        );
                    }
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    actionTakenModal.find('button[type=submit]').attr('disabled',false).text('Save');
                });
                clear_errors('description')
            });


            @if(!$task->is_end)
            $(document).on('click','#proceed-to-next-task', function(){
                Swal.fire({
                    title: 'Are you sure',
                    text: 'Do you want proceed to the next task?',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '{{route('create-next-task',['task' => $task->id])}}',
                            type: 'post',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        }).done(function(response){
                            console.log(response)
                            if(response.success === true){

                                Swal.fire(
                                    response.message,
                                    '',
                                    'success'
                                );

                                setTimeout(function(){
                                    window.location.replace('{{route('request.show',['request' => $task->request_id])}}')
                                },2000)
                            }
                            else{

                                Swal.fire(
                                    response.message,
                                    '',
                                    'warning'
                                );
                            }
                        }).fail(function(xhr, status, error){
                            console.log(xhr)
                        });
                    }
                });
            });
            @endif

        </script>
    @endif

@endpush


