<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fa fa-trash"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Declined</span>
                <span class="info-box-number">{{$declined}}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fa fa-ticket-alt"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Pending</span>
                <span class="info-box-number">{{$pending}}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-purple"><i class="fa fa-hourglass-half"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">On-going</span>
                <span class="info-box-number">{{$on_going}}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fa fa-trophy"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Completed</span>
                <span class="info-box-number">{{$completed}}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
