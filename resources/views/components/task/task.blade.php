@php
    $request_status = "";
        if(!is_null($requestId))
        {
            $request_status = \App\Models\Request::find($requestId)->status;
        }

@endphp
@can('view task')
    <div class="card card-success card-outline">
        <div class="card-body">
            <ul class="nav nav-tabs">
                @if(auth()->user()->can('view task') && !auth()->user()->hasRole('sales director'))
                    <li class="nav-item">
                        <a class="nav-link active text-success" data-toggle="tab" href="#task"><i class="fa fa-thumbtack"></i> Task</a>
                    </li>
                @endif
                @if(auth()->user()->can('view finding') && !auth()->user()->hasRole('sales director'))
                    <li class="nav-item">
                        <a class="nav-link text-success" data-toggle="tab" href="#findings"><i class="fa fa-search"></i> Findings</a>
                    </li>
                @endif
                @if(auth()->user()->can('view commission voucher') && !auth()->user()->hasRole('sales director') && !auth()->user()->hasRole('business administrator') && $request_status != "declined")
                    <li class="nav-item">
                        <a class="nav-link text-success" data-toggle="tab" href="#commission-voucher"><i class="fas fa-file-invoice-dollar"></i> Commission Voucher</a>
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
                                <button class="btn btn-success btn-sm" id="add-finding-btn">Add Findings</button>
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
                @if(auth()->user()->can('view commission voucher') && !auth()->user()->hasRole('sales director') && !auth()->user()->hasRole('business administrator') && $request_status != "declined")
                    <div id="commission-voucher" class="tab-pane fade">
                        <div class="row mt-3">
                            <div class="col-lg-7">
                                <form>
                                    @csrf
                                    <div class="card" id="commission-voucher">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="category">Category</label>
                                                    <select name="category" class="form-select form-control" id="category">
                                                        <option value="">--Select Category--</option>
                                                        <option value="with 10% WHT & 12% VAT">with 10% WHT & 12% VAT</option>
                                                        <option value="with 15% WHT & 12% VAT">with 15% WHT & 12% VAT</option>
                                                        <option value="No Tax Deduction">No Tax Deduction</option>
                                                        <option value="Split Commission">Split Commission</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 mt-3">
                                                    <label for="total_contract_price">TCP</label>
                                                    <input type="number" step="any" class="form-control" name="total_contract_price" id="total_contract_price">
                                                </div>
                                                <div class="col-lg-3 mt-3">
                                                    <label for="sd_rate">SD Rate</label>
                                                    <input type="number" step="any" class="form-control" name="sd_rate" id="sd_rate" max="100" min="0">
                                                </div>
                                                <div class="col-lg-3 mt-3">
                                                    <label for="percentage_released">%</label>
                                                    <input type="number" step="any" class="form-control" name="percentage_released" id="percentage_released" max="100" min="0">
                                                </div>
                                            </div>
                                            <div class="row tax">
                                                <div class="col-lg-6 mt-3">
                                                    <label for="wht">WHT Tax</label>
                                                    <input type="number" step="any" class="form-control" id="wht" disabled>
                                                </div>
                                                <div class="col-lg-6 mt-3">
                                                    <label for="vat">VAT</label>
                                                    <input type="number" step="any" class="form-control" id="vat" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn bg-gray">Compute</button>
                                            <span class="float-right">
                                            <button type="button" class="btn bg-warning" id="add-deduction-btn">Add Deduction</button>
                                        </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-5">
                                <div class="card">
                                    <div class="card-body">
                                        sdf
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

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

    @if(auth()->user()->can('edit commission voucher') && $request_status != "declined")
        <script>
            let commissionVoucher = $('#commission-voucher');
            $(document).on('click','#add-deduction-btn, .plus', function(){
                commissionVoucher.find('.tax').after(`<div class="row deduction">
                <div class="col-5 mt-3"><label>Label</label><input type="text" class="form-control"></div>
                <div class="col-4 mt-3"><label>Amount</label><input type="number" step="any" class="form-control"></div>
                    <div class="col-3 mt-3"><label>&nbsp;</label>
                        <button type=button class="btn plus btn-xs btn-success mt-4" style="margin-top:40px!important;"><i class="fa fa-plus"></i></button>
                        <button type=button class="btn minus btn-xs btn-danger mt-4" style="margin-top:40px!important;"><i class="fa fa-minus"></i></button>
                    </div>
                </div>`);
            })

            $(document).on('click','.minus', function(){
                this.closest('.deduction').remove();
            });
        </script>
    @endif
@endpush
