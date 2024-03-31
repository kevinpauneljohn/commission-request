<div class="card">
    <div class="card-header">
        <h3 class="card-title">Voucher</h3>
    </div>
    <div class="card-body">
        <table id="voucher-list" class="table @if(!is_null($requestId)) table-bordered @endif w-100">
            <thead>
                <tr role="row" class="w-100">
                    <th class="w-75">Details</th>
                    <th class="w-25">Action</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

@push('js')
    <script>
        $(function() {
            $('#voucher-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: '@if(is_null($requestId)){!! route('commission-voucher-lists') !!}@else{!! route('commission-voucher-lists-by-request',['request_id' => $requestId]) !!}@endif',
                columns: [
                    // { data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    { data: 'voucher', name: 'voucher'},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive:true,
                order:[0,'desc'],
                pageLength: 20,
                info:false,
                searching: false,
                lengthChange: false,
                paging: false,
                "drawCallback": function( settings ) {
                    $("#voucher-list thead").remove();
                }
            });
        });

        @can('delete commission voucher')
        $(document).on('click','.delete-voucher-btn', function(){
            let id= this.id;

            Swal.fire({
                title: 'Delete Voucher?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        'url' : '/commission-voucher/'+id,
                        'type' : 'DELETE',
                        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){

                        },success: function(response){
                            if(response.success === true){
                                $('#voucher-list').DataTable().ajax.reload(null, false);
                                commissionVoucherModel = null;
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );

                                let url = window.location.href
                                $('.voucher-preview').find('#save-button-section').load(url+' #save-button-section')
                            }
                        },error: function(xhr, status, error){
                            console.log(xhr);
                        }
                    });

                }
            });
        });
        @endcan

        @can('approve commission voucher')
        $(document).on('click','.approve-voucher-btn', function(){
            let id= this.id;

            Swal.fire({
                title: 'Approve Voucher?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        'url' : '/commission-voucher/approve/'+id,
                        'type' : 'post',
                        'headers': {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){

                        },success: function(response){
                            console.log(response)
                            if(response.success === true){
                                $('#voucher-list').DataTable().ajax.reload(null, false);
                                commissionVoucherModel = null;
                                Swal.fire(
                                    response.message,'','success'
                                );
                            }
                        },error: function(xhr, status, error){
                            console.log(xhr);
                        }
                    });

                }
            });
        });
        @endcan

        @can('edit commission voucher')
            let voucherForm = $('#voucher-payment-form')
            $(document).on('submit','#voucher-payment-form',function(form){
                form.preventDefault();
                let data = $(this).serializeArray();
                Swal.fire({
                    title: 'Do you want to save the payment?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url : '/commission-voucher/payment/'+$('input[name=voucher_id]').val(),
                            type : 'put',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: data,
                            beforeSend: function(){
                                $('.is-invalid').removeClass('is-invalid')
                                $('.text-danger').remove()
                                $('#payment-fields').find('input, select').attr('disabled',true)
                                $('.voucher-payment-btn').attr('disabled',true).html(`<span class="spinner-border spinner-border-sm text-light text-center text-sm" role="status"></span>`)
                            },success: function(response){
                                console.log(response)
                                if(response.success === true){
                                    Swal.fire(
                                        response.message,'','success'
                                    );
                                    $('#voucher-list').DataTable().ajax.reload(null, false);
                                    let url = window.location.href
                                }else{
                                    Swal.fire(
                                        response.message,'','warning'
                                    );
                                }
                            },error: function(xhr, status, error){
                                console.log(xhr);
                                $.each(xhr.responseJSON.errors, function(key, value){
                                    console.log(key)

                                    $('#'+key).addClass('is-invalid')
                                    $('.'+key).append('<p class="text-danger error-'+key+'">'+value+'</p>');
                                });
                            }
                        }).always(function(){
                            $('.voucher-payment-btn').attr('disabled',false).text('Save')
                            $('#payment-fields').find('input, select').attr('disabled',false)
                        });

                    }
                });
            });

            $(document).on('click','.edit-voucher-payment-btn',function(){
                $('.voucher-payment-btn').attr('disabled',false)
            });
            $(document).on('click','.cancel-edit-voucher',function(){
                $('#voucher-list').DataTable().ajax.reload(null, false);
            });

        @endcan
    </script>
@endpush
