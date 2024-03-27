<div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
    <x-task.create-task-button :createButton="true"/>
    <table id="task-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
        <thead>
        <tr role="row">
            <th style="width: 8%;">Task #</th>
            <th>Title</th>
            <th>Assigned To</th>
            <th>Creator</th>
            <th>Date Created</th>
            <th>Due Date</th>
            <th>Status</th>
            <th style="width: 5%;">Action Taken</th>
            <th style="width: 11%;">Action</th>
        </tr>
        </thead>
    </table>
</div>

@if($createButton && auth()->user()->can('add task'))
    <!-- Modal -->
    <div class="modal fade task-modal" id="new-task" tabindex="-1" role="dialog" aria-labelledby="new-task" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form>
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
                        <button type="submit" class="btn btn-success">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

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
                    taskModal.find('form').trigger('reset')
                    taskModal.find('select[name=assign_to]').val('').change()
                    taskModal.find('#description').summernote('reset')
                });
            </script>
        @endcan
    @endif

    @can('view task')
        <script>
            $(function (){
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
                        { data: 'due_date', name: 'due_date'},
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
                            icon: "success",
                            title: response.message
                        });
                        $('#task-list').DataTable().ajax.reload(null, false);
                        $('#request-activities').DataTable().ajax.reload(null, false);
                        taskModal.modal('toggle')
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
                    taskModal.find('button[type=submit]').attr('disabled',false).text('Create');
                });
                clear_errors('title','description','assign_to','due_date');
            })
        </script>
    @endcan

    @if(auth()->user()->can('edit task') || auth()->user()->hasRole('super admin'))
        <script>
            let taskId;
            $(document).on('click','.edit-task-btn', function(){
                taskId = this.id;

                taskModal.find('.modal-title').text('Edit Task');
                taskModal.find('form').attr('id','edit-task-form')
                taskModal.find('.text-danger').remove();
                taskModal.modal('toggle');

                $.ajax({
                    url: '/task/'+taskId+'/edit',
                    type: 'get',
                    beforeSend: function(){
                        taskModal.find('input, select, button').attr('disabled',true);
                        taskModal.find('#description').summernote('disable')
                    }
                }).done(function(response){
                    console.log(response)
                    taskModal.find('input[name=title]').val(response.title)
                    taskModal.find('#description').summernote('code',response.description)
                    taskModal.find('select[name=assign_to]').val(response.assigned_to).change()
                    taskModal.find('input[name=due_date]').val(response.due_date).change()
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                }).always(function(){
                    taskModal.find('input, select, button').attr('disabled',false);
                    taskModal.find('#description').summernote('enable')
                })
            })

            $(document).on('submit','#edit-task-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/task/'+taskId,
                    type: 'put',
                    data: data,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    beforeSend: function(){
                        taskModal.find('input, select, button').attr('disabled',true);
                        taskModal.find('#description').summernote('disable')
                    }
                }).done(function(response){
                    console.log(response)
                    if(response.success === true)
                    {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#task-list').DataTable().ajax.reload(null, false);
                        $('#request-activities').DataTable().ajax.reload(null, false);
                        taskModal.modal('toggle')
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
                    taskModal.find('input, select, button').attr('disabled',false);
                    taskModal.find('#description').summernote('enable')
                })
                clear_errors('title','description','assign_to','due_date');
            })
        </script>
    @endif

    @if(auth()->user()->can('delete task'))
        <script>
            $(document).on('click','.delete-task-btn', function(){
                let id= this.id;

                $tr = $(this).closest('tr');

                let data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();

                Swal.fire({
                    title: 'Delete Task #'+data[0]+'?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            'url' : '/task/'+id,
                            'type' : 'DELETE',
                            'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                if(response.success === true){
                                    $('#task-list').DataTable().ajax.reload(null, false);
                                    $('#request-activities').DataTable().ajax.reload(null, false);

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
@endpush
