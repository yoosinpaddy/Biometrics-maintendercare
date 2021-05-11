<!DOCTYPE html>
<html lang="en">

<head>
    @include('common.dashHeadBar',['url'=>'','title'=>'Edit Streams'])

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{route('default')}}plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{route('default')}}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{route('default')}}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{route('default')}}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{route('default')}}plugins/daterangepicker/daterangepicker.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{route('default')}}dist/css/adminlte.min.css"><!-- Toastr -->
    <link rel="stylesheet" href="{{route('default')}}plugins/toastr/toastr.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('common.dashNav')
        <!-- /.navbar -->
        @include('common.dashSideBar')

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
                                <li class="breadcrumb-item active">Streams</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Main row -->
                    <div class="row">
                        <section class="col-lg-12 connectedSortable">
                            <!-- Custom tabs (Charts with tabs)-->

                            <!-- TO DO List -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Streams</h3>
                                    <div class="project-actions text-right">
                                        <a class="btn btn-primary btn-sm" href="#" data-toggle="modal"
                                            data-target="#modal-addStream">
                                            <i class="fas fa-plus">
                                            </i>
                                            NEW
                                        </a>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">

                                    @if ($errors->any())
                                        <div
                                            class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (sizeof($streams) == 0)
                                                <td></td>
                                                <td>No Streams added yet</td>
                                            @endif
                                            @foreach ($streams as $item)
                                                <tr>
                                                    <td>{{ $item->id }}</td>
                                                    <td>{{ $item->name}}</td>

                                                    <td class="project-actions text-right">
                                                        <a class="btn btn-primary btn-sm"
                                                            href="#edit{{ $item->id }}" data-toggle="modal">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>

                                                        <!-- Sms Modal-->

                                                        <div class="modal fade" id="edit{{ $item->id }}">
                                                            <div class="modal-dialog modal-xl">
                                                                <form id="newParentForm" action="{{ route('update.streams') }}" method='POST'
                                                                    enctype="multipart/form-data">
                                                                    <div class="modal-content">
                                                                        <div id="progressBar" class=" d-flex justify-content-center align-items-center">

                                                                        </div>
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Update Stream</h4>
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
                                                                                                {!! csrf_field() !!}
                                                                                                <div class="form-group">
                                                                                                    <label for="name">Stream Name</label>
                                                                                                    <input required="" type="text" name="name"
                                                                                                        placeholder="Stream Name" id="name" class="form-control"
                                                                                                        value="{{$item->name}}">
                                                                                                    <input id="newOrderId" type="hidden" name="id"
                                                                                                        placeholder="Student first name" class="form-control"
                                                                                                        value="{{$item->id}}">
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

                                                        {{-- <a class="btn btn-primary btn-sm" href="('dashCustomer.withInvoice',['id'=>$item->id])}}">
                                                                <i class="fas fa-money">
                                                                </i>
                                                                Pay
                                                            </a>
                                                            <a class="btn btn-primary btn-sm" href="('dashCustomer.noInvoice',['id'=>$item->id])}}">
                                                                    <i class="fas fa-eye">
                                                                    </i>
                                                                    View
                                                                </a>
                                                                <a class="btn btn-info btn-sm" href="#">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                        <a class="btn btn-danger btn-sm" href="#">
                                                            <i class="fas fa-trash">
                                                            </i>
                                                            Delete
                                                        </a> --}}
                                                    </td>
                                                </tr>

                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="row">{{ $streams->links('pagination::bootstrap-4') }}</div>

                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </section>
                        <!-- /.Left col -->
                        <!-- right col -->
                    </div>
                    <!-- /.row (main row) -->
                </div><!-- /.container-fluid -->

                <div class="modal fade" id="modal-addStream">
                    <div class="modal-dialog modal-xl">
                        <form id="newParentForm" action="{{ route('new.streams') }}" method='POST'
                            enctype="multipart/form-data">
                            <div class="modal-content">
                                <div id="progressBar" class=" d-flex justify-content-center align-items-center">

                                </div>
                                <div class="modal-header">
                                    <h4 class="modal-title">New Stream</h4>
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
                                                            <label for="name">Stream name</label>
                                                            <input required="" type="text" name="name"
                                                                placeholder="Stream name" id="name" class="form-control"
                                                                value="">
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
    <script src="{{route('default')}}js/dateMe.js"></script>
    <!-- date-range-picker -->
    <script src="{{route('default')}}plugins/daterangepicker/daterangepicker.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{route('default')}}plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{route('default')}}plugins/jszip/jszip.min.js"></script>
    <script src="{{route('default')}}plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{route('default')}}plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{route('default')}}plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{route('default')}}plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="{{route('default')}}plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- Toastr -->
    <script src="{{route('default')}}plugins/toastr/toastr.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            bsCustomFileInput.init();
        });
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
                "searching": true,
                "paging": true,
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

        //Date range picker
        $('#orderDeadline').datetimepicker({
            format: 'YYYY-MM-DD  hh:mm:ss',
            useCurrent: false,
            showTodayButton: true,
            showClear: true,
            toolbarPlacement: 'bottom',
            sideBySide: false,
            icons: {
                time: "fa fa-clock",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-clock-o",
                clear: "fa fa-trash-o"
            }
        });

    </script>
    <script>
        var orderSubmitionStep = 1;
        $(function() {
            //hang on event of form with id=myform
            // $("#newParentForm").submit(function(e) {
            //     if(orderSubmitionStep==1){
            //             e.preventDefault();
            //     }
            //     // $("#receiver_id").val(conversationId);
            //     // alert("working");
            //     //prevent Default functionality

            //     //get the action-url of the form
            //     var actionurl = e.currentTarget.action;
            //     if (orderSubmitionStep == 1) {
            //         actionurl = "{{ route('school.new.parent') }}";

            //         $('#progressBar').addClass('overlay');
            //         $('#progressBar').html("<i class='fas fa-2x fa-sync fa-spin'></i>");
            //         //do your own request an handle the results
            //         $.ajax({
            //             url: actionurl,
            //             type: 'post',
            //             dataType: 'html',
            //             data: $("#newParentForm").serialize(),
            //             success: function(data) {
            //                 $('#progressBar').removeClass('overlay');
            //                 $('#progressBar').html("");
            //                 if (data.indexOf('error') <= -1) {
            //                     // alert(data);
            //                     orderSubmitionStep = 2;
            //                     $('#newParentForm').attr('action', '{{ route('school.new.parent') }}');
            //                     $("#newOrderId").val(data);
            //                     $('#generalCol').addClass('d-none');
            //                     $('#filesCol').removeClass('d-none');
            //                 }
            //             },
            //             error: function(request, status, error) {
            //                 $('#progressBar').removeClass('overlay');
            //                 $('#progressBar').html("");
            //                 // alert(request.request);
            //                 if (request.status == 419) {
            //                     console.log(request.status);
            //                     alert("Your session has expired, Please login again");
            //                     window.location.replace(
            //                         "{{ route('school.login') }}");
            //                 }

            //             }
            //         });
            //     } else {
            //     }


            // });


        });
        // window.onload = function() {
        //     initiate();
        //     setInterval(function() {
        //         initiate();
        //     }, 5000);
        // }
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
