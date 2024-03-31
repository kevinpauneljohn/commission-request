<div class="row">
    <!-- /.col -->
    <div class="col-md-3 col-sm-3 col-6">
        <x-adminlte-small-box title="Pending" text="{{$pending}}" icon="fas fa-ticket-alt text-black"
                              theme="warning"/>
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-3 col-6">
        <x-adminlte-small-box title="On-going" text="{{$on_going}}" icon="fas fa-hourglass-half text-black"
                              theme="purple"/>
    </div>
    <div class="col-md-3 col-sm-3 col-6">
        <x-adminlte-small-box title="Delivered" text="{{$delivered}}" icon="fas fa-paper-plane text-black"
                              theme="primary"/>
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-3 col-6">
        <x-adminlte-small-box title="Completed" text="{{$completed}}" icon="fa fa-trophy text-black"
                              theme="success"/>
    </div>
    <!-- /.col -->
</div>
