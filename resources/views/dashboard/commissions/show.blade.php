@extends('adminlte::page')

@section('title', 'Request Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3> Request Details</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item"><a href="{{route('request.index')}}">Requests</a> </li>
                <li class="breadcrumb-item active">Request Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="card card-success card-outline">
                <div class="card-body">
                    <strong><i class="fas fa-ticket-alt"></i> Request # </strong><p class="text-primary text-bold">{{$requestDetail->formatted_id}}</p>
                    <hr>

                    <strong><i class="fas fa-user mr-1"></i> Requester</strong>

                    <p class="text-muted">
                        {{ucwords($requestDetail->user->full_name)}}
                    </p>

                    <hr>

                    <strong><i class="fas fa-calendar-check mr-1"></i> Date Requested</strong>

                    <p class="text-muted">
                        {{$requestDetail->created_at->format('M d, Y g:i:s a')}}
                    </p>
                    <hr>
                    <strong><i class="fa fa-check-square mr-1"></i> Request Type</strong>

                    <p class="text-muted">
                        {{ucwords($requestDetail->request_type)}}
                    </p>

                    <div id="request-status">
                        <hr>
                        <strong><i class="fa fa-tags mr-1"></i> Status</strong>

                        <p class="text-muted">

                            @if(auth()->user()->can('update request status'))
                                <select name="status" class="form-control">
                                    <option value="pending" @if($requestDetail->status === 'pending') selected @endif>Pending</option>
                                    <option value="on-going" @if($requestDetail->status === 'on-going') selected @endif>On-going</option>
                                    <option value="delivered" @if($requestDetail->status === 'delivered') selected @endif>Delivered</option>
                                    <option value="completed" @if($requestDetail->status === 'completed') selected @endif>Completed</option>
                                    <option value="declined" @if($requestDetail->status === 'declined') selected @endif>Declined</option>
                                </select>
                            @else
                                {{ucwords($requestDetail->status)}}
                            @endif

                        </p>
                    </div>
                        @if(collect($requestDetail->commissionVoucher)->count() > 0)
                                @if(!is_null($requestDetail->commissionVoucher->payment_type) && !is_null($requestDetail->commissionVoucher->issuer)
                            && !is_null($requestDetail->commissionVoucher->transaction_reference_no) && !is_null($requestDetail->commissionVoucher->amount_transferred)
                            && $requestDetail->commissionVoucher->is_approved && auth()->user()->can('update request status') && $requestDetail->status !== "delivered" && $requestDetail->status !== "completed" )
                                    <div id="update-status-section">
                                        <hr>
                                        <p class="text-muted">
                                            <button type="button" class="btn btn-success w-100 update-request-status-btn">Update Status</button>
                                        </p>
                                    </div>
                                @endif
                        @endif
                    @if(auth()->user()->hasRole('sales director') && $requestDetail->status == "delivered")
                        <div id="complete-status-section">
                            <hr>
                            <p class="text-muted">
                                <button type="button" class="btn btn-success w-100 complete-request-status-btn">Complete Status</button>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card card-success card-outline">
                <div class="card-header bg-success">
                    <h3 class="card-title">Request Hierarchy</h3>
                </div>
                <div class="card-body">
                    @if(!is_null($requestDetail->parent_request_id))
                        <a href="{{route('request.show',['request' =>$requestDetail->parent_request_id])}}" class="mb-3"><p>
                                <span class="text-bold text-success">{{$requestDetail->parent_request}}</span> <i class="text-orange">Parent</i></p></a>
                    @endif

                        <p><span class="text-muted">{{$requestDetail->formatted_id}}</span> <i class="text-orange">Current</i></p>

                    @if(collect($down_lines)->count() == 1)
                        <h5>Child Request</h5>
                        @elseif( collect($down_lines)->count()> 1)
                        <h5>Child Requests</h5>
                    @endif

                    @foreach($down_lines as $down_line)
                        <a href="{{route('request.show',['request' => $down_line->id])}}"><p>{{$down_line->formatted_id}}</p></a>
                    @endforeach
                </div>
            </div>

            <div class="card card-success card-outline">
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                        <table id="request-activities" class="table table-bordered table-hover" role="grid" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th></th>
                                <th>Activities</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card card-success card-outline request-details">
                <form id="update-request-details-form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mt-3">
                                <label for="buyer">Buyer</label>
                                <input type="text" id="buyer" class="form-control" value="{{ucwords(strtolower($requestDetail->buyer->firstname).' '.strtolower($requestDetail->buyer->lastname))}}" readonly>
                            </div>
                            <div class="col-lg-4 mt-3">
                                <label for="project">Project</label>
                                <input type="text" id="project" class="form-control" value="{{ucwords(strtolower($requestDetail->project))}}" readonly>
                            </div>
                            <div class="col-lg-4 mt-3">
                                <label for="model_unit">Phase/Block/Lot</label>
                                @php
                                    $unitLocation ="";
                                    if(!is_null($requestDetail->phase))
                                        {
                                            $unitLocation .= 'Phase '.$requestDetail->phase.'  ';
                                        }
                                    $unitLocation .= 'Block '.$requestDetail->block.'  Lot '.$requestDetail->lot;
                                @endphp
                                <input type="text" id="model_unit" class="form-control" value="{{ucwords($unitLocation)}}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mt-3 total_contract_price">
                                <label for="total_contract_price">Total Contract Price</label>
                                <input name="total_contract_price" type="number" step="any" id="total_contract_price" min="0" class="form-control" value="{{$requestDetail->total_contract_price}}"
                                       @if($requestDetail->status == 'declined' || $requestDetail->status == 'completed' || $requestDetail->status == 'delivered' || auth()->user()->hasRole('sales director'))disabled @endif>

                            </div>
                            <div class="col-lg-4 mt-3 financing">
                                <label for="financing">Financing</label><span class="required">*</span>
                                <select name="financing" class="form-control" id="financing" @if($requestDetail->status == 'declined' || $requestDetail->status == 'completed' || $requestDetail->status == 'delivered' || auth()->user()->hasRole('sales director'))disabled @endif>
                                    <option value="">-- Select Financing --</option>
                                    <option value="hdmf">HDMF</option>
                                    <option value="bank">Bank</option>
                                    <option value="inhouse">Inhouse</option>
                                    <option value="deferred">Deferred Cash</option>
                                    <option value="nhmfc">NHMFC</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>
                            <div class="col-lg-4 mt-3 sd_rate">
                                <label for="sd_rate">Sales Director Rate</label>
                                <select name="sd_rate" id="sd_rate" class="form-control" @if($requestDetail->status == 'declined' || $requestDetail->status == 'completed' || $requestDetail->status == 'delivered' || auth()->user()->hasRole('sales director'))disabled @endif>
                                    <option value="">-- Select SD Rate --</option>
                                    @php $rate = 0.5; $increment = 0.5;@endphp
                                    @for($count = 1; $rate <= 5.5 ;$count++)
                                        @php $rate = $rate + $increment; @endphp
                                        <option value="{{$rate}}">{{$rate}}%</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 mt-3 message">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-left text-muted">Message</span>
                                        <span class="info-box-number text-muted">{{$requestDetail->message}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($requestDetail->status == 'pending' || $requestDetail->status == 'on-going' && !auth()->user()->hasRole('sales director'))
                    <div class="card-footer">
                        <button type="submit" class="btn btn-default">Save</button>
                    </div>
                    @endif
                </form>
            </div>

            <x-task.task :assignee="$assignee" :createButton="true" :requestId="$requestDetail->id"/>

        </div>
    </div>


@stop
<x-device-checker />
@section('plugins.Sweetalert2',true)
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{asset('js/clear_errors.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('#financing').val('{{$requestDetail->financing}}').change();
            $('#sd_rate').val('{{$requestDetail->sd_rate}}').change();
        })

        $('#request-activities').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('request-activities',['requestId' => $requestDetail->id]) !!}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex'},
                { data: 'description', name: 'description'},
            ],
            responsive:true,
            order:[0,'desc'],
            pageLength: 5,
            lengthChange: false,
            info:false,
            searching: false
        });


        @if('edit request')
            $(document).on('submit','#update-request-details-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '{{route('request.update',['request' => $requestDetail->id])}}',
                    type: 'put',
                    data: data,
                    beforeSend: function(){
                        $('#update-request-details-form').find('button[type=submit]').attr('disabled',true).text('Saving...')
                    }
                }).done(function(response){
                    if(response.success === true)
                    {
                        Swal.fire(
                            response.message,
                            '',
                            'success'
                        );
                        $('#request-activities').DataTable().ajax.reload(null, false);

                        grossCommission()
                        sub_total_update()

                        $.each(response.data, function(key, value){
                            $('#commission-voucher').find('#'+key).val(value)
                        })
                    }
                    else{
                        Swal.fire(
                            response.message,
                            '',
                            'warning'
                        );
                    }
                }).fail(function(xhr, status, error){
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always(function(){
                    $('#update-request-details-form').find('button[type=submit]').attr('disabled',false).text('Save')
                });
            })

            $(document).ready(function(){
                grossCommission()
                use_reference_amount()
            })

            $(document).on('input','input[name=sub_total]',function(){
                let percentage_released_value = 0
                if(this.value !== "")
                {
                    let gross_commission_field = parseFloat($('input[name=gross_commission]').val())
                    let sub_total_field = parseFloat(this.value)

                    percentage_released_value = (sub_total_field / gross_commission_field) * 100
                }

                $('input[name=percentage_released]').val(percentage_released_value.toFixed(2));
            })

            const subTotal = () => {
                return grossCommission()
            }

            const percentageReleased = (percent) => {
                let released = subTotal() * percent;
                return released.toFixed(2)
            }
            const grossCommission = () => {
                let tcp = parseFloat($('#total_contract_price').val())
                let sd_rate = parseFloat($('#sd_rate').val()) / 100
                let gross_comm = tcp * sd_rate

                $('#commission-voucher').find('input[name=gross_commission]').val(gross_comm.toFixed(2))
                return gross_comm;
            }

            const sub_total_update = () => {
                $('input[name=percentage_released]').change();
            }

            const sub_total_input_update = (percentage_released_value) => {
                let percent = 0
                if(percentage_released_value !== "")
                {
                    percent = parseFloat(percentage_released_value) / 100;
                }

                // console.log(percentageReleased(percent))
                $('#commission-voucher').find('input[name=sub_total]').val(percentageReleased(percent))
            }

            $(document).on('input','input[name=percentage_released]',function(){
                sub_total_input_update(this.value)
            })
            $(document).on('change','input[name=percentage_released]',function(){
                sub_total_input_update(this.value)
            })

            const use_reference_amount = () => {
                let reference_amount_field_row = $('.reference_amount_field_row')
                let tcp_basis = $('.tcp_basis')
                if($('#reference_amount_for_wht').is(":checked"))
                {
                    reference_amount_field_row.show();
                    reference_amount_field_row.
                    find('#reference_amount, #percentage_released_reference_amount, #sub_total_reference_amount, #remarks').attr('disabled',false);

                    tcp_basis.hide()
                    tcp_basis.find('#percentage_released, #sub_total').attr('disabled',true);
                }else{
                    reference_amount_field_row.hide();
                    reference_amount_field_row.find('#reference_amount, #percentage_released_reference_amount, #sub_total_reference_amount, #remarks').attr('disabled',true);

                    tcp_basis.show()
                    tcp_basis.find('#percentage_released, #sub_total').attr('disabled',false);
                }
            }

            $(document).on('change','#reference_amount_for_wht',function (){
                use_reference_amount()
            })

        @endif

        @if(auth()->user()->can('update request status') && !auth()->user()->hasRole('sales director'))
            $(document).on('click','.update-request-status-btn', function(){
            Swal.fire({
                title: 'Update Request Status?',
                text: "Mark status as DELIVERED.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        url : '{{route('request-delivered',['request' => $requestDetail->id])}}',
                        type : 'put',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){
                            $('.update-request-status-btn').attr('disabled',true).text('Updating...')
                        },success: function(response){
                            console.log(response)
                            if(response.success === true){
                                let url = window.location.href
                                $('#update-status-section').load(url+' #update-status-section')
                                $('#request-status').load(url+' #request-status')
                                $('#task-list').DataTable().ajax.reload(null, false);

                                Swal.fire(
                                    response.message,'','success'
                                );

                            }else{
                                Swal.fire(
                                    response.message,'','warning'
                                );
                            }
                        },error: function(xhr, status, error){
                            console.log(xhr);
                        }
                    }).always(function(){
                        $('.update-request-status-btn').attr('disabled',false).text('Update Status')
                    });

                }
            });
            })
        @endif

        @if(auth()->user()->hasRole('sales director') && $requestDetail->status == "delivered")
        $(document).on('click','.complete-request-status-btn', function(){
            Swal.fire({
                title: 'Request Completed?',
                text: "Mark status as COMPLETED.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        url : '{{route('request-completed',['request' => $requestDetail->id])}}',
                        type : 'put',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){

                        },success: function(response){
                            console.log(response)
                            if(response.success === true){
                                let url = window.location.href
                                $('#complete-status-section').load(url+' #complete-status-section')
                                $('#request-status').load(url+' #request-status')


                                Swal.fire(
                                    response.message,'','success'
                                );

                            }else{
                                Swal.fire(
                                    response.message,'','warning'
                                );
                            }
                        },error: function(xhr, status, error){
                            console.log(xhr);
                        }
                    }).always(function(){

                    });

                }
            });
        })
        @endif

        @if(auth()->user()->can('update request status'))
            let currentStatus = $('select[name=status]').val();
            $(document).on('change','select[name=status]', function(){
                let requestStatus = $(this).val();
                Swal.fire({
                    title: 'Update Status?',
                    text: "Mark status as "+requestStatus+".",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url : '{{route('request-status-update',['request' => $requestDetail->id])}}',
                            type : 'put',
                            data: {"status" : requestStatus},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function(){

                            },success: function(response){
                                console.log(response)
                                if(response.success === true){

                                    setTimeout(function(){
                                        window.location.reload();
                                    },1500)

                                    Swal.fire(
                                        response.message,'','success'
                                    );

                                }else{
                                    Swal.fire(
                                        response.message,'','warning'
                                    );
                                }
                            },error: function(xhr, status, error){
                                console.log(xhr);
                            }
                        }).always(function(){

                        });

                    }else{
                        $('select[name=status]').val(currentStatus);
                    }
                });
            });
        @endif
    </script>
@stop
