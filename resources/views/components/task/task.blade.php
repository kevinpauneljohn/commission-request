@php
    $request = "";
        if(!is_null($requestId))
        {
            $request = \App\Models\Request::find($requestId);
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
                @if(auth()->user()->can('view commission voucher') && !auth()->user()->hasRole('sales director') && !auth()->user()->hasRole('business administrator') && $request->status != "declined")
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
                @if(auth()->user()->can('view commission voucher') && !auth()->user()->hasRole('sales director') && !auth()->user()->hasRole('business administrator') && $request->status != "declined")
                    <div id="commission-voucher" class="tab-pane fade">
                        <div class="row mt-3">
                            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                                <form id="commission-computation-form">
                                    @csrf
                                    <div class="card" id="commission-voucher">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="category">Category</label>
                                                    <select name="category" class="form-select form-control" id="category">
                                                        <option value="">--Select Category--</option>
                                                        <option value="Corporate Broker's Tax Deduction">Corporate Broker's Tax Deduction</option>
                                                        <option value="Individual Broker's Tax Deduction">Individual Broker's Tax Deduction</option>
                                                        <option value="No Tax Deduction">No Tax Deduction</option>
                                                        <option value="Split Commission">Split Commission</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-9 mt-3 total_contract_price">
                                                    <label for="total_contract_price">TCP</label>
                                                    <input type="number" step="any" class="form-control" name="total_contract_price" id="total_contract_price" value="{{$request->total_contract_price}}" disabled>
                                                </div>
                                                <div class="col-lg-3 mt-3 sd_rate">
                                                    <label for="sd_rate">SD Rate</label>
                                                    <input type="number" step="any" class="form-control" name="sd_rate" id="sd_rate" max="100" min="0" value="{{$request->sd_rate}}" disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 mt-3 gross_commission">
                                                    <label for="gross_commission">Gross Commission</label>
                                                    <input type="number" step="any" class="form-control" name="gross_commission" id="gross_commission" max="5000000" min="0" disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 mt-3 reference_amount_for_wht">
                                                    <input type="checkbox" id="reference_amount_for_wht" name="reference_amount_checkbox">
                                                    <label for="reference_amount_for_wht"></label> <span>Select other amount for wht</span>
                                                </div>
                                            </div>
                                            <div class="row reference_amount_field_row" style="display: none;">
                                                <div class="col-lg-6 mt-3 reference_amount">
                                                    <label for="reference_amount">Ref. Amt WHT</label>
                                                    <input type="number" step="any" class="form-control" name="reference_amount" id="reference_amount" max="5000000" min="0" value="0" disabled>
                                                </div>
                                                <div class="col-lg-6 mt-3 remarks">
                                                    <label for="remarks">Remarks</label>
                                                    <input type="text" step="any" class="form-control" name="remarks" id="remarks" disabled>
                                                </div>
                                            </div>
                                            <div class="row reference_amount_field_row" style="display: none;">
                                                <div class="col-lg-5 mt-3 percentage_released_reference_amount">
                                                    <label for="percentage_released_reference_amount">% Released ref</label>
                                                    <input type="number" step="any" class="form-control" name="percentage_released_reference_amount" id="percentage_released_reference_amount" max="100" min="0" value="0">
                                                </div>
                                                <div class="col-lg-7 mt-3 sub_total_reference_amount">
                                                    <label for="sub_total_reference_amount">Sub Total ref</label>
                                                    <input type="number" step="any" class="form-control" name="sub_total_reference_amount" id="sub_total_reference_amount" max="5000000" min="0" value="0">
                                                </div>
                                            </div>
                                            <div class="row tcp_basis">
                                                <div class="col-lg-5 mt-3 percentage_released">
                                                    <label for="percentage_released">% Released</label>
                                                    <input type="number" step="any" class="form-control" name="percentage_released" id="percentage_released" max="100" min="0" value="0">
                                                </div>
                                                <div class="col-lg-7 mt-3 sub_total">
                                                    <label for="sub_total">Sub Total</label>
                                                    <input type="number" step="any" class="form-control" name="sub_total" id="sub_total" max="5000000" min="0" value="0">
                                                </div>
                                            </div>
                                            <div class="row tax">
                                                <div class="col-lg-6 mt-3 wht">
                                                    <label for="wht">WHT Tax</label>
                                                    <input type="number" name="wht" step="any" class="form-control" id="wht" disabled min="0" value="0">
                                                </div>
                                                <div class="col-lg-6 mt-3 vat">
                                                    <label for="vat">VAT</label>
                                                    <input type="number" name="vat" step="any" class="form-control" id="vat" disabled min="12" max="12" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="request_id" value="{{$requestId}}">
                                        <div class="card-footer">
                                            <button type="submit" class="btn bg-gray">Preview</button>
                                            <span class="float-right">
                                            <button type="button" class="btn bg-warning" id="add-deduction-btn">Add Deduction</button>
                                        </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="card voucher-preview">
                                    <div class="card-body preview table-responsive-xl">
                                        <table class="table table-bordered table-hover">
                                            <tr>
                                                <th class="text-center" colspan="4">RNH Realty & Management Inc. Comm Voucher</th>
                                            </tr>
                                            <tr>
                                                <th id="project-name" colspan="4" class="text-center">Madonna Residences</th>
                                            </tr>
                                            <tr>
                                                <td>Req. #</td>
                                                <td id="payee" class="text-bold" colspan="3">{{$request->formatted_id}}</td>
                                            </tr>
                                            <tr>
                                                <td>Payee</td>
                                                <td id="payee" class="text-bold">{{ucwords($request->user->full_name)}}</td>
                                                <td>Amount:</td>
                                                <td id="amount" class="text-bold"></td>
                                            </tr>
                                            <tr>
                                                <td class="w-25">Client</td>
                                                <td id="client" class="text-bold w-25">{{ucwords($request->buyer_full_name)}}</td>
                                                <td>In Words:</td>
                                                <td id="amount-in-words" class="text-bold w-50"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="table-active" ></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">TCP</td>
                                                <td colspan="1" id="tcp"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">SD Rate (4%)</td>
                                                <td colspan="1" id="sd-rate"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Gross Commission</td>
                                                <td colspan="1" id="gross-commission"></td>
                                            </tr>
                                            <tr id="tax-basis-row" style="display: none;">
                                                <td colspan="3">Basis Tax/Lot Price</td>
                                                <td colspan="1" id="tax-basis"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><span id="percent-released"></span>% Released</td>
                                                <td colspan="1" id="released-gross-commission"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Withholding Tax <span id="wht-percent"></span></td>
                                                <td colspan="1" id="wht"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">VAT <span id="vat-percent"></span></td>
                                                <td colspan="1" id="vat-amount"></td>
                                            </tr>
                                            <tr class="net-commission">
                                                <td colspan="3">Net Commission</td>
                                                <td colspan="1" id="net-commission"></td>
                                            </tr>
                                            <tr id="row-separator">
                                                <td colspan="4" class="table-active"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Prepared By</td>
                                                <td colspan="1" id="prepared_by"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Approved By</td>
                                                <td colspan="1"></td>
                                            </tr>
                                        </table>
                                        @if(is_null($request->commissionVoucher))
                                            <button type="button" class="btn btn-success mt-3 w-100" id="save-voucher-btn">Save Voucher</button>
                                          @endif
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
                            icon: "success",
                            title: response.message
                        });
                        $('#findings-list').DataTable().ajax.reload(null, false);
                        $('#request-activities').DataTable().ajax.reload(null, false);
                        findingModal.modal('toggle')
                        findingModal.find('form').trigger('reset');
                    }else if(response.success === false)
                    {
                        Toast.fire({
                            icon: "warning",
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

    @if(auth()->user()->can('edit commission voucher') && $request->status != "declined")
        <script>
            let commissionVoucher = $('#commission-voucher');
            $(document).on('click','#add-deduction-btn, .plus', function(){
                commissionVoucher.find('.tax').after(`<div class="row deduction">
                <div class="col-5 mt-3"><label>Deduction</label><input type="text" name="deduction_title[]" class="form-control"></div>
                <div class="col-4 mt-3"><label>Amount</label><input type="number" name="deduction_amount[]" step="any" class="form-control"></div>
                    <div class="col-3 mt-3"><label>&nbsp;</label>
                        <button type=button class="btn plus btn-xs btn-success mt-4" style="margin-top:40px!important;"><i class="fa fa-plus"></i></button>
                        <button type=button class="btn minus btn-xs btn-danger mt-4" style="margin-top:40px!important;"><i class="fa fa-minus"></i></button>
                    </div>
                </div>`);
            })

            $(document).on('click','.minus', function(){
                this.closest('.deduction').remove();
            });

            $(document).on('change','#commission-voucher select[name=category]',function(){
                let value = this.value;
                category(value)
            });

            const category = (value) => {
                let wht = $('input[name=wht]');
                let vat = $('input[name=vat]');
                switch (value) {
                    case "Corporate Broker's Tax Deduction":
                        wht.val(15).attr('disabled',false)
                        vat.val(12).attr('disabled',false)
                        break;
                    case "Individual Broker's Tax Deduction":
                        wht.val(10).attr('disabled',false)
                        vat.val(12).attr('disabled',false)
                        break;
                    default: $('input[name=wht], input[name=vat]').val(0).attr('disabled',true)
                }
            }

            const reference_amount_for_wht_value = () => {
                return parseFloat($('input[name=reference_amount]').val());
            }

            $(document).on('input','input[name=percentage_released_reference_amount]',function(){
                let percentage_released_reference_amount = 0
                if(this.value !== "")
                {
                    percentage_released_reference_amount = parseFloat(this.value) / 100
                }

                let sub_total_ref_value = reference_amount_for_wht_value() * percentage_released_reference_amount

                $('input[name=sub_total_reference_amount]').val(sub_total_ref_value.toFixed(2));
            })

            $(document).on('input','input[name=reference_amount]',function(){
                let reference_amount = 0
                let percentage_released_reference_amount = 0
                let percentage_released_element = $('input[name=percentage_released_reference_amount]');
                if(this.value !== "")
                {
                    reference_amount = parseFloat(this.value)
                }

                if(percentage_released_element.val() !== "")
                {
                    percentage_released_reference_amount = parseFloat(percentage_released_element.val()) / 100
                }

                let sub_total_ref_value = reference_amount * percentage_released_reference_amount

                $('input[name=sub_total_reference_amount]').val(sub_total_ref_value.toFixed(2));
            })

            $(document).on('input','input[name=sub_total_reference_amount]',function(){
                let sub_total_reference_amount = 0
                let reference_amount = 0
                let reference_amount_element = $('input[name=reference_amount]');
                let percentage_released_element = $('input[name=percentage_released_reference_amount]');
                if(this.value !== "")
                {
                    sub_total_reference_amount = parseFloat(this.value)

                }

                if(reference_amount_element.val() !== "")
                {
                    reference_amount = parseFloat(reference_amount_element.val())
                }

                let percentage_released_reference_amount = (sub_total_reference_amount / reference_amount) * 100

                percentage_released_element.val(percentage_released_reference_amount.toFixed(2));
            })

            let commissionVoucherModel = null;
            let voucherPreview = $('.voucher-preview')
            $(document).on('submit','#commission-computation-form', function(form){
                form.preventDefault()
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/commission-voucher-preview',
                    type: 'get',
                    data: data,
                    dataType: 'json',
                    beforeSend: function(){
                        voucherPreview.find('.deduction-lists, .total-balance').remove()
                        voucherPreview.prepend(`<div class="overlay">
                                        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                    </div>`)
                    }
                }).done(function(response){
                    console.log(response)
                    commissionVoucherModel = response;

                    if(response.tax_basis_reference === true)
                    {
                        $('#tax-basis-row').show();
                        $('#tax-basis').text(response.tax_basis_reference_amount);
                    }else{
                        $('#tax-basis-row').hide();
                    }
                    voucherPreview.find('#request_number').text(response.request_number)
                    voucherPreview.find('#amount').text(response.commission_receivable)
                    voucherPreview.find('#amount-in-words').text(response.commission_in_words)
                    voucherPreview.find('#tcp').text(response.tcp)
                    voucherPreview.find('#gross-commission').text(response.gross_commission)
                    voucherPreview.find('#percent-released').text(response.percentage_released)
                    voucherPreview.find('#released-gross-commission').text(response.released_gross_commission)
                    voucherPreview.find('#sd-rate').text(response.sd_rate)
                    voucherPreview.find('#wht-percent').text(response.with_holding_tax)
                    voucherPreview.find('#wht').text(response.with_holding_tax_amount)
                    voucherPreview.find('#vat-percent').text(response.vat)
                    voucherPreview.find('#vat-amount').text(response.vat_amount)
                    voucherPreview.find('#net-commission').text(response.total_commission)
                    voucherPreview.find('#prepared_by').text(response.prepared_by)

                    if(response.deductions > 0)
                    {
                        $.each(response.deduction_lists, function(key, value){
                            voucherPreview.find('.net-commission').after(`<tr class="deduction-lists">
                                <td colspan="3">${key}</td><td colspan="1" class="text-danger">${value}</td>
                            </tr>`)
                        })
                        voucherPreview.find('.deduction-lists').last().after(`<tr class='total-balance table-active'>
                            <td colspan="3">Total Commission Balance</td>
                            <td colspan="1">${response.commission_receivable}</td>
                            </tr>`)
                    }
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                }).always(function(){
                    voucherPreview.find('.overlay').remove()
                })
            })

            $(document).on('click','#save-voucher-btn',function(){
                $.ajax({
                    url: '{{route('commission-voucher.store')}}',
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: commissionVoucherModel,
                    beforeSend: function(){
                        voucherPreview.find('#save-voucher-btn').attr('disabled',true).html(`<span class="spinner-border spinner-border-sm text-light text-center text-sm" role="status"></span>`)
                    }
                }).done(function(response){
                    // console.log(response)
                    if(response.success === true)
                    {
                        Swal.fire(
                            response.message,
                            '',
                            'success'
                        );
                        $('#save-voucher-btn').fadeOut()
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
                }).always(function(){
                    voucherPreview.find('#save-voucher-btn').attr('disabled',false).text('Save Voucher')
                })
            });
        </script>
    @endif
@endpush
