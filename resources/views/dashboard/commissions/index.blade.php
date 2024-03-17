@extends('adminlte::page')

@section('title', 'Commission Request')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Requests</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a> </li>
                <li class="breadcrumb-item active">Requests</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        @can('add request')
            <div class="card-header">
                <button class="btn btn-primary btn-sm" id="add-request-btn">Create Request</button>
            </div>
        @endcan
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                <table id="request-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                    <thead>
                    <tr role="row">
                        <th style="width: 10%;">Request #</th>
                        <th>Date Requested</th>
                        <th style="width: 15%;">Buyer</th>
                        <th>Project</th>
                        @if(auth()->user()->hasRole('sales director'))
                            <th>Model Unit</th>
                            <th>Phase/Block/Lot</th>
                        @endif
                        <th style="width: 8%;">Total Contract Price</th>
                        @if(auth()->user()->hasRole('sales director'))
                            <th>Financing</th>
                            <th>SD Rate</th>
                            <th>Cheque No.</th>
                            <th>Issuing Bank</th>
                            <th style="width: 8%;">Cheque Amount</th>
                        @endif
                        <th>Requester</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @can('add request')
        <!-- Modal -->
        <div class="modal fade request-modal" id="new-request" tabindex="-1" role="dialog" aria-labelledby="new-request" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form id="add-request-form">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <fieldset class=" mb-5">
                                <legend>Buyer</legend>
                                <div class="row">
                                    <div class="col-lg-4 firstname">
                                        <label for="firstname">First Name</label><span class="required">*</span>
                                        <input type="text" name="firstname" class="form-control" id="firstname">
                                    </div>
                                    <div class="col-lg-4 middlename">
                                        <label for="middlename">Middle Name</label> <i>(optional)</i>
                                        <input type="text" name="middlename" class="form-control" id="middlename">
                                    </div>
                                    <div class="col-lg-4 lastname">
                                        <label for="lastname">Last Name</label><span class="required">*</span>
                                        <input type="text" name="lastname" class="form-control" id="lastname">
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset>
                                <legend>Details</legend>
                                <div class="row mt-3">
                                    <div class="col-lg-6 project">
                                        <label for="project">Project</label><span class="required">*</span>
                                        <input type="text" name="project" class="form-control" id="project">
                                    </div>
                                    <div class="col-lg-6 model_unit">
                                        <label for="model_unit">Model Unit</label><span class="required">*</span>
                                        <input type="text" name="model_unit" class="form-control" id="model_unit">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-4 phase">
                                        <label for="phase">Phase</label> <i>(optional)</i>
                                        <input type="text" name="phase" class="form-control" id="phase">
                                    </div>
                                    <div class="col-lg-4 block">
                                        <label for="block">Block</label><span class="required">*</span>
                                        <input type="text" name="block" class="form-control" id="block">
                                    </div>
                                    <div class="col-lg-4 lot">
                                        <label for="lot">Lot</label><span class="required">*</span>
                                        <input type="text" name="lot" class="form-control" id="lot">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6 total_contract_price">
                                        <label for="total_contract_price">Total Contract Price</label><span class="required">*</span>
                                        <input type="number" step="any" name="total_contract_price" class="form-control" id="total_contract_price">
                                    </div>
                                    <div class="col-lg-6 financing">
                                        <label for="financing">Financing</label><span class="required">*</span>
                                        <select name="financing" class="form-control" id="financing">
                                            <option value="">-- Select Financing --</option>
                                            <option value="hdmf">HDMF</option>
                                            <option value="bank">Bank</option>
                                            <option value="inhouse">Inhouse</option>
                                            <option value="deferred">Deferred Cash</option>
                                            <option value="nhmfc">NHMFC</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6 request_type">
                                        <label for="request_type">Request Type</label><span class="required">*</span>
                                        <select name="request_type" id="request_type" class="form-control">
                                            <option value="">--Select Request Type</option>
                                            <option value="commission_request">Commission Request</option>
                                            <option value="cheque_pickup">Commission Cheque Pick Up</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 sd_rate">
                                        <label for="sd_rate">Sales Director Rate</label> <i>(optional)</i>
                                        <select name="sd_rate" id="sd_rate" class="form-control">
                                            <option value="">-- Select SD Rate --</option>
                                            @php $rate = 0.5; $increment = 0.5;@endphp
                                            @for($count = 1; $rate <= 5.5 ;$count++)
                                                @php $rate = $rate + $increment; @endphp
                                                <option value="{{$rate}}">{{$rate}}%</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="row cheque_details">
                                    <div class="col-lg-4 cheque_number mt-3">
                                        <label for="cheque_number">Cheque Number</label><span class="required">*</span>
                                        <input type="number" id="cheque_number" name="cheque_number" class="form-control">
                                    </div>
                                    <div class="col-lg-4 bank_name mt-3">
                                        <label for="bank_name">Bank name</label><span class="required">*</span>
                                        <input type="text" id="bank_name" name="bank_name" class="form-control">
                                    </div>
                                    <div class="col-lg-4 cheque_amount mt-3">
                                        <label for="cheque_amount">Cheque Amount</label><span class="required">*</span>
                                        <input type="number" step="any" id="cheque_amount" name="cheque_amount" class="form-control">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-12 message">
                                        <label for="message">Custom Message</label> <i>(optional)</i>
                                        <textarea name="message" class="form-control" id="message" style="min-height: 200px;"></textarea>
                                    </div>
                                </div>
                            </fieldset>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
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

    @can('view request')
        <script>
            $(function() {
                $('#request-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('request-list') !!}',
                    columns: [
                        { data: 'id', name: 'id'},
                        { data: 'created_at', name: 'created_at'},
                        { data: 'buyer', name: 'buyer'},
                        { data: 'project', name: 'project'},
                            @if(auth()->user()->hasRole('sales director'))
                        { data: 'model_unit', name: 'model_unit'},
                        { data: 'phase_block_lot', name: 'phase_block_lot'},
                            @endif
                        { data: 'total_contract_price', name: 'total_contract_price'},
                            @if(auth()->user()->hasRole('sales director'))
                        { data: 'financing', name: 'financing'},
                        { data: 'sd_rate', name: 'sd_rate'},
                        { data: 'cheque_number', name: 'cheque_number'},
                        { data: 'bank_name', name: 'bank_name'},
                        { data: 'cheque_amount', name: 'cheque_amount'},
                           @endif
                        { data: 'user_id', name: 'user_id'},
                        { data: 'status', name: 'status'},
                        { data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive:true,
                    order:[0,'desc'],
                    pageLength: 50
                });
            });
        </script>
    @endcan

    @can('add request')
        <script>
            let requestModal = $('.request-modal');
            let requestTable = $('#request-list');

            $(document).ready(function (){
                $('#request_type').val("").change();
            });

            $(document).on('click','#add-request-btn', function(){
                requestModal.modal('toggle');
                requestModal.find('.modal-title').text('Create Request');
            });

            let request = "";
            $(document).on('change','#request_type',function(){
                 request = $(this).val();
                console.log(request);

                if(request === "" || request === "commission_request")
                {
                    $('.cheque_details').attr('style','display:none;');
                }else{
                    $('.cheque_details').removeAttr('style');
                }
            })


            $(document).on('submit','#add-request-form', function(form){
                form.preventDefault();
                let data = $(this).serializeArray();

                $.ajax({
                    url: '/request',
                    type: 'post',
                    data: data,
                    beforeSend: function(){
                        requestModal.find('button[type=submit]').attr('disabled',true).text('Creating...')
                    }
                }).done( (response) => {
                    console.log(response)
                    if(response.success === true)
                    {
                        Toast.fire({
                            type: "success",
                            title: response.message
                        });
                        requestTable.DataTable().ajax.reload(null, false);
                        $('#add-request-form').trigger('reset')
                        requestModal.modal('toggle')
                    }else{
                        Toast.fire({
                            type: "warning",
                            title: response.message
                        });
                    }
                }).fail( (xhr, status, error) => {
                    console.log(xhr)
                    $.each(xhr.responseJSON.errors, function(key, value){
                        let element = $('.'+key);

                        element.find('.error-'+key).remove();
                        element.append('<p class="text-danger error-'+key+'">'+value+'</p>')
                    });
                }).always( () => {
                    requestModal.find('button[type=submit]').attr('disabled',false).text('Create')
                });

                clear_errors('firstname','lastname','project','model_unit','block','lot','total_contract_price','financing','request_type','sd_rate','cheque_number','bank_name','cheque_amount');
            });

        </script>
    @endcan
    <script>
        $('.select2').select2();
    </script>
@stop
