<label for="task" class="mr-2">Display</label>
<select class="form-select w-25" name="task" id="task">
    <option value="" @if(is_null($display_task)) selected @endif>All Task</option>
    <option value="My Task" @if(!is_null($display_task)) selected @endif>My Task</option>
</select>

@push('js')
    @can('view task')
        <script>
            $(document).on('change','select[name=task]',function(){
                let value = this.value;
                $.ajax({
                    url: '{{route('display-task')}}',
                    type: 'post',
                    data: {task : value},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                }).done(function(response){
                    console.log(response)
                    $('#task-list').DataTable().ajax.reload(null, false);
                }).always(function(){

                });
            })
        </script>
    @endcan
@endpush
