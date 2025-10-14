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
    <x-request.info-box />
    <div class="card card-success card-outline">

            <div class="card-header">
                @can('add request')
                    <button class="btn btn-success btn-sm mr-3" id="add-request-btn">Create Request</button>
                @endcan

                <x-request.display />
            </div>

        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <table id="request-list" class="table table-bordered table-hover" role="grid" style="width: 100%">
                    <thead>
                    <tr role="row">
                        <th style="width: 10%;">Request #</th>
                        <th>Date Requested</th>
                        <th style="width: 15%;">Buyer</th>
                        <th>Project</th>
                        <th>Model Unit</th>
                        <th>Phase/Block/Lot</th>
                        <th style="width: 8%;">TCP</th>
                        @if(!auth()->user()->hasRole('sales director'))
                            <th>Requester</th>
                        @endif
                        <th>Parent #</th>
                        <th style="min-width: 150px;">Progress</th>
                        <th>% Released</th>
                        <th>total %</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
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
                    @if(isset($_GET['parent_request']))
                        <input type="hidden" name="parent_request_id" value="{{$_GET['parent_request']}}">
                    @endif
                    <div class="modal-content">
                        <div class="overlay-wrapper"></div>
                        <div class="modal-header bg-success">
                            <h5 class="modal-title">Modal title</h5>
                            @if(!isset($_GET['parent_request']) || isset($_GET['remaining']))
                                <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            @endif

                        </div>
                        <div class="modal-body">
                            @if(isset($_GET['parent_request']))
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="alert alert-info">
                                            <i class="fa fa-exclamation-circle"></i> Parent Request <span class="text-bold text-yellow" id="parent-request-formatted-id"></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(!auth()->user()->hasRole('sales director'))
                                <div class="row">
                                    <div class="col-lg-12 sales_director mb-3">
                                        <label for="sales_director">Sales Director</label>
                                        <select class="form-control" name="sales_director" id="sales_director" style="width: 100%">
                                            <option value=""></option>
                                            @foreach($salesDirectors as $salesDirector)
                                                <option value="{{$salesDirector->id}}">{{ucwords($salesDirector->full_name)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
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
                                <div class="row mt-3">
                                    <div class="col-lg-12 message">
                                        <label for="message">Custom Message</label> <i>(optional)</i>
                                        <textarea name="message" class="form-control" id="message" style="min-height: 200px;"></textarea>
                                    </div>
                                </div>
                            </fieldset>

                        </div>
                        <div class="modal-footer">
                            @if(!isset($_GET['parent_request']) || isset($_GET['remaining']))
                                <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Close</button>
                            @endif
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@stop
<x-device-checker />
@section('plugins.Sweetalert2',true)
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{asset('js/clear_errors.js')}}"></script>

    @can('view request')
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
            $(function() {
                $('#sales_director').select2({
                    dropdownParent: $('.request-modal'),
                    placeholder: "Select an option",
                    allowClear: true
                });

                $('#request-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('request-list') !!}',
                    columns: [
                        { data: 'id', name: 'id'},
                        { data: 'created_at', name: 'created_at'},
                        { data: 'buyer', name: 'buyer'},
                        { data: 'project', name: 'project'},
                        { data: 'model_unit', name: 'model_unit'},
                        { data: 'phase_block_lot', name: 'phase_block_lot'},
                        { data: 'total_contract_price', name: 'total_contract_price'},
                            @if(!auth()->user()->hasRole('sales director'))
                            { data: 'user_id', name: 'user_id'},
                            @endif
                        { data: 'parent_request', name: 'parent_request'},
                        { data: 'progress', name: 'progress'},
                        { data: 'percent_released', name: 'percent_released'},
                        { data: 'total_released', name: 'total_released'},
                        { data: 'status', name: 'status'},
                        { data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive:true,
                    order:[0,'desc'],
                    pageLength: 10,
                    drawCallback: function(row){
                        let request = row.json;

                        $('#request-list').find('tbody')
                            .append('<tr style="background-color:#e3fdfa;"><td colspan="14" style="font-size:20px;"><span class="text-bold">Total Amount @if(auth()->user()->hasRole('sales director'))Received @else Transferred @endif: </span> <span class="text-success text-bold">&#8369;'+parseFloat(request.total_completed_released).toLocaleString(2)+'</span></td></tr>')
                    }
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
                requestModal.modal({
                    backdrop: 'static',
                    keyboard: false  // to prevent closing with Esc button (if you want this too)
                });
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
                        Swal.fire({
                            title: response.message,
                            text: '@if(isset($_GET['parent_request'])) Redirecting now... @else  @endif ',
                            icon: "success",
                            showConfirmButton: @if(isset($_GET['parent_request'])) false @else true @endif,
                        });
                        requestTable.DataTable().ajax.reload(null, false);
                        $('#add-request-form').trigger('reset')
                        requestModal.modal('toggle')

                        @if(isset($_GET['parent_request']))
                            setTimeout(function(){
                                window.location.replace('/request/'+response.request_id)
                            },2000)
                        @endif

                    }else{
                        Toast.fire({
                            icon: "warning",
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

                clear_errors('sales_director','firstname','lastname','project','model_unit','block','lot','total_contract_price','financing','request_type','sd_rate','cheque_number','bank_name','cheque_amount');
            });

        </script>
    @endcan
    <script>
        $('.select2, #sales_director').select2();
        let requestModel;
    </script>

    @if(isset($_GET['parent_request']))
        <script>

            $(function(){
                $.ajax({
                    url: '/request/get-parent/{{$_GET['parent_request']}}',
                    type: 'get',
                    beforeSend: function(){
                        requestModal.find('.overlay-wrapper').html('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>')
                    }
                }).done(function(response, status, xhr){
                    console.log(xhr)
                    requestModel = response;
                    if(xhr.status === 200 && requestModel !== null)
                    {
                        if(requestModel.status === 'completed' || requestModel.status === 'declined') {
                            requestModal.find('.modal-title').text('Create Request')
                            requestModal.find('#sales_director').val(requestModel.user_id).change()
                            requestModal.find('input[name=firstname]').val(requestModel.buyer.firstname)
                            requestModal.find('input[name=middlename]').val(requestModel.buyer.middlename)
                            requestModal.find('input[name=lastname]').val(requestModel.buyer.lastname)
                            requestModal.find('input[name=project]').val(requestModel.project)
                            requestModal.find('input[name=model_unit]').val(requestModel.model_unit)
                            requestModal.find('input[name=phase]').val(requestModel.phase)
                            requestModal.find('input[name=block]').val(requestModel.block)
                            requestModal.find('input[name=lot]').val(requestModel.lot)
                            requestModal.find('input[name=total_contract_price]').val(requestModel.total_contract_price)
                            requestModal.find('select[name=financing]').val(requestModel.financing).change()
                            requestModal.find('select[name=request_type]').val(requestModel.request_type).change()
                            requestModal.find('select[name=sd_rate]').val(requestModel.sd_rate).change()
                            requestModal.find('textarea[name=message]').html(requestModel.message)
                            requestModal.find('#parent-request-formatted-id').text(requestModel.formatted_id)
                        }else{
                            requestModal.find('input[name=parent_request_id], .alert-info').remove()
                        }
                    }
                }).fail(function(xhr, status, error){
                    console.log(xhr)
                    if(xhr.status === 404)
                    {
                        requestModal.find('#parent-request-formatted-id').text('Not Found')
                        requestModal.find('input[name=parent_request_id]').remove()
                    }
                }).always(function(){
                    requestModal.find('.overlay-wrapper').html('')
                });
                // requestModal.modal('toggle');
                requestModal.modal({
                    backdrop: 'static',
                    keyboard: false  // to prevent closing with Esc button (if you want this too)
                });
                requestModal.modal('toggle');
            })

            $(document).on('click','.modal-close',function(){
                window.history.pushState({page: "another"}, "another page", "{{route('request.index')}}");
                requestModel = null;
                requestModal.find('form').trigger('reset')
                requestModal.find('textarea').val('')
                requestModal.find('input[name=parent_request_id], .alert-info').remove()
            })
        </script>
    @endif
    <script>

        let pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: 'ap1'
        });

        let channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            // console.log(JSON.stringify(data));
            const Toast = Swal.mixin({
                toast: true,
                position: "top-right",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            console.log(data.message.status)
            if(data.message.status === "pending")
            {
                Toast.fire({
                    icon: "success",
                    title: "A new request as been created!"
                });
            }else{
                Toast.fire({
                    icon: "success",
                    title: "Request status has been updated!"
                });
            }

            requestTable.DataTable().ajax.reload(null, false);
        });
    </script>
@stop
