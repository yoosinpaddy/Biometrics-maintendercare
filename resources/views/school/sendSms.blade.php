<!DOCTYPE html>
<html lang="en">

<head>
    @include('common.dashHeadBar',['url'=>'','title'=>'Send Bulk Sms'])

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{route('default')}}/plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{route('default')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{route('default')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{route('default')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{route('default')}}/plugins/daterangepicker/daterangepicker.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{route('default')}}/dist/css/adminlte.min.css"><!-- Toastr -->
    <link rel="stylesheet" href="{{route('default')}}/plugins/toastr/toastr.min.css">
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
                                <li class="breadcrumb-item active">Bulky Sms</li>
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
                                    <h3 class="card-title">Select Target Group and send sms</h3>
                                    <div class="project-actions text-right">
                                    </div>
                                </div>
                                <div class="card-header">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <form id="searchForm"
                                        action="{{ route('school.send_bulk_sms') }}"
                                        method='POST' enctype="multipart/form-data">
                                        <div class="modal-content">

                                                <section class="content">
                                                    <div class="row">
                                                        <div class="col-md-12"
                                                            id="generalCol">
                                                            <div class="card card-primary">
                                                                <div class="card-body">
                                                                    {!! csrf_field() !!}
                                                                    <div class="form-group">
                                                                        <div class="row">
                                                                            <div class="col-lg-6">

                                                                                <label for="class">Class</label>
                                                                                <select required="" name="class" id="class"
                                                                                    class="form-control custom-select">
                                                                                    <option value="all">all</option>
                                                                                    @foreach ($classes as $class)
                                                                                        <option value="{{ $class->class }}">
                                                                                            {{ $class->class  }}
                                                                                        </option>

                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-6">

                                                                                <label for="stream">Stream</label>
                                                                                <select required="" name="stream" id="stream"
                                                                                    class="form-control custom-select">
                                                                                    <option value="all">all</option>
                                                                                    @foreach ($streams as $stream)
                                                                                        <option value="{{ $stream->id }}">
                                                                                            {{ $stream->name  }}
                                                                                        </option>

                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-12">

                                                                                <label for="stream">Message</label>
                                                                                <textarea class="form-control" name="message" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <!-- /.card-body -->
                                                            </div>
                                                            <!-- /.card -->
                                                        </div>

                                                    </div>
                                                </section>
                                            <div
                                                class="modal-footer justify-content-between">
                                                <button type="submit"
                                                    class="btn btn-primary">Send</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </form>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">

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
    <script src="{{route('default')}}/js/dateMe.js"></script>
    <!-- date-range-picker -->
    <script src="{{route('default')}}/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{route('default')}}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{route('default')}}/plugins/jszip/jszip.min.js"></script>
    <script src="{{route('default')}}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{route('default')}}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{route('default')}}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{route('default')}}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="{{route('default')}}/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- Toastr -->
    <script src="{{route('default')}}/plugins/toastr/toastr.min.js"></script>
    <!-- Page specific script -->
    <script>

    </script>
    <script>
        window.onload = function() {
            @if (\Session::has('success'))
                toastr.success("{{ \Session::get('success') }}");
            @elseif (\Session::has('message'))
                toastr.error("{{ \Session::get('message') }}");
            @endif
        }

    </script>

    <script>
        $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
        });

    </script>
</body>

</html>
