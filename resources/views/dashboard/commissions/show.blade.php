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

                    <hr>
                    <strong><i class="fa fa-tags mr-1"></i> Status</strong>

                    <p class="text-muted">
                        {{ucwords($requestDetail->status)}}
                    </p>
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
                                <input name="total_contract_price" type="number" step="any" id="total_contract_price" min="0" class="form-control" value="{{$requestDetail->total_contract_price}}"
                                       @if($requestDetail->status == 'declined' || $requestDetail->status == 'completed' || $requestDetail->status == 'delivered' && !auth()->user()->hasRole('sales director'))disabled @endif>

                            </div>
                            <div class="col-lg-4 mt-3 financing">
                                <label for="financing">Financing</label><span class="required">*</span>
                                <select name="financing" class="form-control" id="financing" @if($requestDetail->status == 'declined' || $requestDetail->status == 'completed' || $requestDetail->status == 'delivered' && !auth()->user()->hasRole('sales director'))disabled @endif>
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
                                <select name="sd_rate" id="sd_rate" class="form-control" @if($requestDetail->status == 'declined' || $requestDetail->status == 'completed' || $requestDetail->status == 'delivered' && !auth()->user()->hasRole('sales director'))disabled @endif>
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
        @endif
    </script>
@stop
