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
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <strong><i class="fas fa-ticket-alt"></i> <span class="text-primary text-bold">{{$id}}</span></strong>
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

                    <hr>
                    <strong><i class="fa fa-tags mr-1"></i> Status</strong>

                    <p class="text-muted">
                        {{ucwords($requestDetail->status)}}
                    </p>
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
            <div class="card card-info card-outline">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mt-3">
                            <label for="buyer">Buyer</label>
                            <input type="text" id="buyer" class="form-control" value="{{ucwords($requestDetail->buyer->firstname.' '.$requestDetail->buyer->lastname)}}" readonly>
                        </div>
                        <div class="col-lg-4 mt-3">
                            <label for="project">Project</label>
                            <input type="text" id="project" class="form-control" value="{{ucwords($requestDetail->project)}}" readonly>
                        </div>
                        <div class="col-lg-4 mt-3">
                            <label for="model_unit">Model Unit</label>
                            <input type="text" id="model_unit" class="form-control" value="{{ucwords($requestDetail->model_unit)}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mt-3">
                            <label for="buyer">Buyer</label>
                            <input type="text" id="buyer" class="form-control" value="{{ucwords($requestDetail->buyer->firstname.' '.$requestDetail->buyer->lastname)}}" readonly>
                        </div>
                        <div class="col-lg-4 mt-3">
                            <label for="project">Project</label>
                            <input type="text" id="project" class="form-control" value="{{ucwords($requestDetail->project)}}" readonly>
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
                            <input name="total_contract_price" type="number" step="any" id="total_contract_price" min="0" class="form-control" value="{{$requestDetail->total_contract_price}}">
                        </div>
                        <div class="col-lg-4 mt-3 financing">
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
                        <div class="col-lg-4 mt-3">
                            <label for="sd_rate">Sales Director Rate</label>
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

                    @if($requestDetail->request_type == "cheque_pickup")
                        <div class="row">
                            <div class="col-lg-4 mt-3 cheque_number">
                                <label for="cheque_number">Cheque #</label>
                                <input type="number" id="cheque_number" class="form-control" value="{{$requestDetail->cheque_number}}" readonly>
                            </div>
                            <div class="col-lg-4 mt-3 bank_name">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" id="bank_name" class="form-control" value="{{ucwords($requestDetail->bank_name)}}" readonly>
                            </div>
                            <div class="col-lg-4 mt-3 cheque_amount">
                                <label for="cheque_amount">Cheque Amount</label>
                                <input type="text" id="cheque_amount" class="form-control" value="{{number_format($requestDetail->cheque_amount,2)}}" readonly>
                            </div>
                        </div>
                    @endif
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
                <div class="card-footer">
                    <button type="submit" class="btn btn-default">Save</button>
                </div>
            </div>

            <x-task.task :assignee="$assignee" :createButton="true" :requestId="$requestDetail->id"/>
        </div>
    </div>


@stop
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
            pageLength: 50,
            info:false,
            paging: false,
            searching: false
        });

    </script>
@stop
