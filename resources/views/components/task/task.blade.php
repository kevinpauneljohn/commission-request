@can('view task')
    <div class="card card-purple card-outline">
        <div class="card-body">
            <ul class="nav nav-tabs">
                @if(auth()->user()->can('view task') && !auth()->user()->hasRole('sales director'))
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#task"><i class="fa fa-thumbtack"></i> Tasks</a>
                    </li>
                @endif
                @if(auth()->user()->can('view down line leads') && !auth()->user()->hasRole('sales director'))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#findings"><i class="fa fa-search"></i> Findings</a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                <div id="task" class="tab-pane active">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        @if($createButton)
                            <div class="card-tools mb-5 mt-4">
                                <button class="btn btn-primary btn-sm" id="create-task-btn">Create task</button>
                            </div>
                        @endif
                        <table id="task-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th>Task #</th>
                                <th>Title</th>
                                <th>Assigned To</th>
                                <th>Creator</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th>Action Taken</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div id="findings" class="tab-pane fade">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="card-tools mb-5 mt-4">
                            <button class="btn btn-primary btn-sm" id="create-task-btn">Add Findings</button>
                        </div>
                        <table id="findings-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th style="width: 10%;">Date Created</th>
                                <th>Findings</th>
                                <th style="width: 20%;">Author</th>
                                <th style="width: 10%;"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endcan

@if($createButton)
    @can('add task')
        <!-- Modal -->
        <div class="modal fade task-modal" id="new-task" tabindex="-1" role="dialog" aria-labelledby="new-task" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form>
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
                                    <label for="assign_to">Assign to</label><span class="required">*</span>
                                    <select class="form-select" id="assign_to" name="assign_to" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($assignee as $user)
                                            <option value="{{$user->id}}">{{$user->full_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mt-3 due_date">
                                    <label for="due_date">Due Date</label>
                                    <input type="text" name="due_date" id="due_date" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="request_id" value="{{$requestId}}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endif


@section('plugins.SummerNote',true)
@section('plugins.Moment',true)
@section('plugins.DateRangePicker',true)

@push('js')
    @if($createButton)
        <script>
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
                    dropdownParent: $('.task-modal'),
                    placeholder: 'Select an assignee',
                    allowClear: true,
                });


                $('input[name="due_date"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    drops: 'up',
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'),10)
                }, function(start, end, label) {
                    var years = moment().diff(start, 'years');
                    console.log("You are " + years + " years old!");
                });

            });

        </script>

        @can('add task')
            <script>
                let taskModal = $('.task-modal');
                $(document).on('click','#create-task-btn', function(){
                    taskModal.modal('toggle');
                    taskModal.find('.modal-title').text('Create Task');
                    taskModal.find('form').attr('id','add-task-form')
                });
            </script>
        @endcan
    @endif

    @can('view task')
        <script>
            $(function() {
                $('#task-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: @if(is_null($requestId))'{!! route('task-list') !!}'@else'{!! route('get-task-by-request',['requestId' => $requestId]) !!}'@endif ,
                    columns: [
                        { data: 'id', name: 'id'},
                        { data: 'title', name: 'title'},
                        { data: 'assigned_to', name: 'assigned_to'},
                        { data: 'creator', name: 'creator'},
                        { data: 'created_at', name: 'created_at'},
                        { data: 'status', name: 'status'},
                        { data: 'action_taken', name: 'action_taken'},
                        { data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive:true,
                    order:[0,'desc'],
                    pageLength: 50
                });
            });
        </script>
    @endcan

    @can('add task')
        <script>
            $(document).on('submit','#add-task-form',function(form){
                form.preventDefault()
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/task',
                    type: 'post',
                    data: data,
                    beforeSend: function (){
                        taskModal.find('button[type=submit]').attr('disabled',true).text('Creating...');
                    }
                }).done(function (response){
                    console.log(response)
                    if(response.success === true)
                    {
                        Toast.fire({
                            type: "success",
                            title: response.message
                        });
                        $('#task-list').DataTable().ajax.reload(null, false);
                        $('#request-activities').DataTable().ajax.reload(null, false);
                        taskModal.modal('toggle')
                    }else if(response.success === false)
                    {
                        Toast.fire({
                            type: "warning",
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
                    taskModal.find('button[type=submit]').attr('disabled',false).text('Create');
                });
                clear_errors('title','description','assign_to','due_date');
            })
        </script>
    @endcan
@endpush
