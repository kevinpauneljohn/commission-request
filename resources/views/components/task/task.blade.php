@can('view task')
    <div class="card card-success card-outline">
        <div class="card-body">
            <ul class="nav nav-tabs">
                @if(auth()->user()->can('view task') && !auth()->user()->hasRole('sales director'))
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#task"><i class="fa fa-thumbtack"></i> Task</a>
                    </li>
                @endif
                @if(auth()->user()->can('view finding') && !auth()->user()->hasRole('sales director'))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#findings"><i class="fa fa-search"></i> Findings</a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                <div id="task" class="tab-pane active">
                    <x-task.table-list :assignee="$assignee" :requestId="$requestId" :createButton="true"/>
                </div>
                <div id="findings" class="tab-pane fade">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        @can('add finding')
                            <div class="card-tools mb-5 mt-4">
                                <button class="btn btn-primary btn-sm" id="add-finding-btn">Add Findings</button>
                            </div>
                        @endcan
                        <table id="findings-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th style="width: 1%;"></th>
                                <th>Date Created</th>
                                <th style="width: 40%;">Findings</th>
                                <th>Author</th>
                                <th style="width: 5%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endcan



@can('add finding')
        <!-- Modal -->
        <div class="modal fade finding-modal" id="new-finding" tabindex="-1" role="dialog" aria-labelledby="new-finding" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form>
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Findings</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mt-3 findings">
                                    <label for="findings">Description</label><span class="required">*</span> <i>(max of 5000 characters)</i>
                                    <textarea id="findings" name="findings" class="form-control" style="min-height: 300px;"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="request_id" value="{{$requestId}}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan


@section('plugins.SummerNote',true)
@section('plugins.Moment',true)
@section('plugins.DateRangePicker',true)

@push('js')


    @can('view task')
        <script>
            $(function() {


                $('#findings-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('finding-list',['requestId' => $requestId]) !!}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        { data: 'created_at', name: 'created_at'},
                        { data: 'findings', name: 'findings'},
                        { data: 'user_id', name: 'user_id'},
                        { data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive:true,
                    order:[0,'desc'],
                    pageLength: 20
                });
            });
        </script>
    @endcan

    @can('add finding')
        <script>
            let findingModal = $('.finding-modal');
            $(document).on('click','#add-finding-btn', function(){
                findingModal.modal('toggle');
                findingModal.find('.modal-title').text('Add Findings');
                findingModal.find('form').attr('id','add-finding-form')
            });

            $(document).on('submit','#add-finding-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();
                console.log(data)

                $.ajax({
                    url:'/finding',
                    type: 'post',
                    data: data,
                    beforeSend: function(){
                        findingModal.find('button[type=submit]').attr('disabled',true).text('Saving...');
                    }
                }).done(function(response){
                    console.log(response)
                    if(response.success === true)
                    {
                        Toast.fire({
                            type: "success",
                            title: response.message
                        });
                        $('#findings-list').DataTable().ajax.reload(null, false);
                        $('#request-activities').DataTable().ajax.reload(null, false);
                        findingModal.modal('toggle')
                        findingModal.find('form').trigger('reset');
                    }else if(response.success === false)
                    {
                        Toast.fire({
                            type: "warning",
                            title: response.message
                        });
                    }
                }).fail(function (xhr, status, error){
                    console.log(xhr)
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    findingModal.find('button[type=submit]').attr('disabled',false).text('Save');
                });
                clear_errors('findings');
            });
        </script>
    @endcan
@endpush
