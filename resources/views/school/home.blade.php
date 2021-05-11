<!DOCTYPE html>
<html lang="en">

<head>
    @include('common.dashHeadBar',['url'=>'','title'=>'DashBoard:'.Auth::user()->id.':'.Auth::user()->name])

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="./plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="./plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="./plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./dist/css/adminlte.min.css"><!-- Toastr -->
    <link rel="stylesheet" href="./plugins/toastr/toastr.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('common.dashNav',['url'=>''])
        <!-- /.navbar -->
        @include('common.dashSideBar',['url'=>''])

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Home</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"></h3>
                            <div class="project-actions text-right">
                                <a class="btn btn-primary btn-sm" href="#" data-toggle="modal"
                                    data-target="#modal-addStudent">
                                    <i class="fas fa-plus">
                                    </i>
                                    NEW STUDENT
                                </a>
                            </div>
                            @if ($errors->any())
                                <p class="text-danger">{{ $errors->first() }}</p>
                            @endif
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        @foreach ($formated_classes as $class)

                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{{ $class['_count'] }}</h3>

                                        <p>Grade {{$class['class'].' '.$class['stream']}} Enrolment</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <a href="{{ route('school.class.data',['class_name'=>$class['class'],'stream_id'=>$class['stream_id']]) }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        @endforeach
                        {{-- <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $inProgress }}</h3>

                                    <p>In Progress</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ route('school.inProgress') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $submitted }}</h3>

                                    <p>Delivered</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ route('school.submitted') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $complete }}</h3>

                                    <p>Complete</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ route('school.complete') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                    </div> --}}
                    <!-- /.row -->
                    <!-- Main row -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-4 connectedSortable" style="display:none">

                            <!-- /.card-header -->
                            <div class="card-body" id="mysearch">
                                <table id="nameList" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Joshua</td>
                                        </tr>
                                        <tr>
                                            <td>Ken</td>
                                        </tr>
                                        <tr>
                                            <td>Admin</td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </section>
                        <!-- Left col -->
                        <section class="col-lg-8 connectedSortable">
                            <!-- Custom tabs (Charts with tabs)-->

                            <!-- DIRECT CHAT -->
                            <div id="chatAreaContactsControll" class="card direct-chat direct-chat-primary">

                                <!-- /.card-footer-->
                            </div>
                            <!--/.direct-chat -->

                        </section>
                        <!-- Left col -->
                        <section class="col-lg-12 connectedSortable">
                            <!-- Custom tabs (Charts with tabs)-->

                            <!-- /.card -->
                        </section>
                        <!-- /.Left col -->
                        <!-- right col -->
                    </div>
                    <!-- /.row (main row) -->
                <div class="modal fade" id="modal-addStudent">
                    <div class="modal-dialog modal-xl">
                        <form id="newParentForm" action="{{ route('school.new.student') }}" method='POST'
                            enctype="multipart/form-data">
                            <div class="modal-content">
                                <div id="progressBar" class=" d-flex justify-content-center align-items-center">

                                </div>
                                <div class="modal-header">
                                    <h4 class="modal-title">New Student</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <section class="content">
                                        <div class="row">
                                            <div class="col-md-12" id="generalCol">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Info</h3>

                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool"
                                                                data-card-widget="collapse" title="Collapse">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        @if ($errors->any())
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        {!! csrf_field() !!}
                                                        <div class="form-group">
                                                            <label for="fname">Student first name</label>
                                                            <input required="" type="text" name="fname"
                                                                placeholder="First name" id="fname" class="form-control"
                                                                value="">
                                                            <input id="newOrderId" type="hidden" name="newOrderId"
                                                                placeholder="Student first name" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="surname">Student Surname</label>
                                                            <input required="" type="text" name="surname"
                                                                placeholder="Student Surname" id="surname"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="phone">Student middle name(optional)</label>
                                                            <input type="text" name="middle_name"
                                                                placeholder="Student middle name(optional)" id="phone"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="upi">Student UPI No.</label>
                                                            <input required="" type="text" name="upi_no"
                                                                placeholder="Student  UPI No." id="upi"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="upi">Class/Grade</label>
                                                            <input required="" type="number" name="class"
                                                                placeholder="Class/Grade" id="upi"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="student_id">Stream</label>
                                                            <select required="" name="stream" id="student_id"
                                                                class="form-control custom-select">
                                                                @if (count($streams)==0)
                                                                <option value="">Go to <a href="{{route('school.streams')}}">Stream setup</a> to add stream</option>

                                                                @else
                                                                    <option value="">Select one</option>
                                                                @endif
                                                                @foreach ($streams as $stream)
                                                                    <option value="{{ $stream->id }}">
                                                                        {{ $stream->name}}
                                                                    </option>

                                                                @endforeach
                                                            </select>
                                                        </div>

                                                    </div>
                                                    <!-- /.card-body -->
                                                </div>
                                                <!-- /.card -->
                                            </div>

                                        </div>
                                    </section>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </form>
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        @include('common.dashFooter')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @include('common.dashScripts',['url'=>''])

    <!-- jQuery -->
    <script src="./js/dateMe.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="./plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="./plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="./plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="./plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="./plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="./plugins/jszip/jszip.min.js"></script>
    <script src="./plugins/pdfmake/pdfmake.min.js"></script>
    <script src="./plugins/pdfmake/vfs_fonts.js"></script>
    <script src="./plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="./plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="./plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- Page specific script -->
    <!-- Toastr -->
    <script src="./plugins/toastr/toastr.min.js"></script>
    <script>
        $(function() {
            $("#nameList").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "ordering": false,
                "paging": false,
                "lengthChange": false,
            }).searching().container().appendTo('#mysearch .col-md-12:eq(0)');
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
        $(document).ready(function() {
            $('#nameList_filter').parent().removeClass('col-md-6');
            $('#nameList_filter').parent().addClass('col-md-12');
        });

    </script>
    <script>
        var conversationId = 1;
        $(function() {
            //hang on event of form with id=myform
            $("#contact_us").submit(function(e) {
                // $("#receiver_id").val(conversationId);
                // alert("working");
                //prevent Default functionality
                e.preventDefault();

                //get the action-url of the form
                var actionurl = e.currentTarget.action;

                //do your own request an handle the results
                $.ajax({
                    url: actionurl,
                    type: 'post',
                    dataType: 'html',
                    data: $("#contact_us").serialize(),
                    success: function(data) {
                        if (data.indexOf('error":false') > -1) {
                            resetReload();
                            initiate();
                        }
                    }
                });

            });

        });

        function resetReload() {
            var message = document.getElementById("message_input").value
            var content =
                "<div class='direct-chat-msg right'><div class='direct-chat-infos clearfix'><span class='direct-chat-name float-right'>Me</span><span class='direct-chat-timestamp float-left'>" +
                getDate() +
                "</span> </div><img class='direct-chat-img' src='dist/img/boxed-bg.png' alt='message user image'><div class='direct-chat-text'>" +
                message + "</div></div>";

            document.getElementById("contact_us").reset();
            var objDiv = document.getElementById("chatDiv");
            objDiv.innerHTML += content;
            objDiv.scrollTop = objDiv.scrollHeight;
        }

        function getDate() {
            var d = new Date();
            return d.toLocaleTimeString();
        }




        window.onload = function() {
            @if (\Session::has('success'))
                toastr.success("{{ \Session::get('success') }}");
            @elseif (\Session::has('message'))
                toastr.error("{{ \Session::get('message') }}");
            @endif
        }


    </script>
</body>

</html>
