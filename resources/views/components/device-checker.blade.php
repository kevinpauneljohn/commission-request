@push('css')
    @if(!$agent->isDesktop())
        <style>
            #multi-task-menu{
                display: none;
            }
        </style>
    @endif
@endpush
