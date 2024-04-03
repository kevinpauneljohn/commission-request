<span class="text-bold mr-2 mt-2">Display</span>
<select name="request_display" id="request_display" class="form-select" style="height: 30px;%!important;">
    <option value="" @if(is_null($display_request)) selected @endif>All</option>
    <option value="pending" @if($display_request == 'pending') selected @endif>Pending</option>
    <option value="on-going" @if($display_request == 'on-going') selected @endif>On-going</option>
    <option value="delivered" @if($display_request == 'delivered') selected @endif>Delivered</option>
    <option value="completed" @if($display_request == 'completed') selected @endif>Completed</option>
    <option value="declined" @if($display_request == 'declined') selected @endif>Declined</option>
</select>

@push('js')
    <script>
        $(document).on('change','#request_display', function(){
            let value = this.value;

            $.ajax({
                url: '{{route('display-request')}}',
                type: 'post',
                data: {display: value},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response){
                    console.log(response)
                    $('#request-list').DataTable().ajax.reload(null, false);
                }
            })
        });

    </script>
@endpush
