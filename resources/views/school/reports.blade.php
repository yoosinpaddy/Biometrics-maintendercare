<!DOCTYPE html>
<html lang="en">

<head>
    @include('common.dashHeadBar',['url'=>'','title'=>$title])

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{route('default')}}/dist/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{route('default')}}/plugins/toastr/toastr.min.css">
    <style>
        /*
        *
        * ==========================================
        * CUSTOM UTIL CLASSES
        * ==========================================
        *
        */
        .datepicker td, .datepicker th {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.85rem;
        }

        .datepicker {
            margin-bottom: 3rem;
        }
    </style>
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
                                <li class="breadcrumb-item active">Reports</li>
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
                                    <h3 class="card-title">Register Reports</h3>
                                    <div class="project-actions text-right">
                                    </div>
                                </div>
                                <div class="card-header">
                                    <form id="searchForm"
                                        action="{{ route('school.reports.poster') }}"
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
                                                                            <div class="col-lg-4">

                                                                                <label for="class">Class</label>
                                                                                <select required="" name="class" id="class"
                                                                                    class="form-control custom-select">
                                                                                    <option value="{{$current_class}}">{{$current_class}}</option>
                                                                                    @foreach ($classes as $class)
                                                                                        <option value="{{ $class->class }}">
                                                                                            {{ $class->class  }}
                                                                                        </option>

                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-4">

                                                                                <label for="stream">Stream</label>
                                                                                <select required="" name="stream" id="stream"
                                                                                    class="form-control custom-select">
                                                                                    <option value="{{$current_streamv}}">{{$current_stream}}</option>
                                                                                    @foreach ($streams as $stream)
                                                                                        <option value="{{ $stream->id }}">
                                                                                            {{ $stream->name  }}
                                                                                        </option>

                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-4  mx-auto">
                                                                                <label for="stream">Date</label>
                                                                                <div class="form-group mb-4">
                                                                                    <div class="datepicker date input-group p-0 shadow-sm">
                                                                                        <input name="day" value="{{$day}}" type="text" placeholder="Choose Date" class="form-control py-4 px-4" id="reservationDate">
                                                                                        <div class="input-group-append"><span class="input-group-text px-4"><i class="fa fa-clock-o"></i></span></div>
                                                                                    </div>
                                                                                </div>
                                                                                {{-- <input type="text" class="form-control" placeholder="Date Of Birth" id="dob"> --}}
                                                                                {{-- <div class="input-group-append">
                                                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span> --}}

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
                                                    class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </form>
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
                                                <th>UPI</th>
                                                <th>Name</th>
                                                <th>Temperature</th>
                                                <th>Time(event)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (sizeof($myRecords) == 0)
                                                <td></td>
                                                <td>No Reports added yet</td>
                                            @endif
                                            @foreach ($myRecords as $item)
                                                <tr>
                                                    <td>{{ $item->upi_no }}</td>

                                                    <td>
                                                        {{ $item->student->first_name . ' ' . $item->student->surname }}
                                                    </td>
                                                    <td>
                                                        {{ $item->temperature }}
                                                    </td>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $now = $item->time_taken;
                                                            $date = date("Y-m-d h:i a",($now/1000));
                                                            $new_time = date("Y-m-d h:i a", strtotime('+3 hours', strtotime($date)));
                                                            echo $new_time;
                                                        @endphp
                                                        ({{$item->status=='enter'?'Arrival':'Departure'}})
                                                    </td>

                                                </tr>

                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>UPI</th>
                                                <th>Name</th>
                                                <th>Temperature</th>
                                                <th>Time(event)</th>
                                            </tr>
                                        </tfoot>
                                        </table>
                                        <div class="row">{{ $myRecords->links('pagination::bootstrap-4') }}</div>

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

                <div class="modal fade" id="modal-addParent">
                    <div class="modal-dialog modal-xl">
                        <form id="newParentForm" action="{{ route('school.new.parent') }}" method='POST'
                            enctype="multipart/form-data">
                            <div class="modal-content">
                                <div id="progressBar" class=" d-flex justify-content-center align-items-center">

                                </div>
                                <div class="modal-header">
                                    <h4 class="modal-title">New Parent</h4>
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
                                                            <label for="fname">Guardian first name</label>
                                                            <input required="" type="text" name="fname"
                                                                placeholder="First name" id="fname" class="form-control"
                                                                value="">
                                                            <input id="newOrderId" type="hidden" name="newOrderId"
                                                                placeholder="Guardian first name" class="form-control"
                                                                value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="surname">Guardian Surname</label>
                                                            <input required="" type="text" name="surname"
                                                                placeholder="Guardian Surname" id="surname"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="phone">Guardian Phone</label>
                                                            <input required="" type="text" name="phone"
                                                                placeholder="Guardian Phone" id="phone"
                                                                class="form-control" value="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="student_id">Student</label>
                                                            <select required="" name="student_id" id="student_id"
                                                                class="form-control custom-select">
                                                                <option value="">Select One</option>
                                                                @foreach ($allStudents as $student)
                                                                    <option value="{{ $student->id }}">
                                                                        {{ $student->first_name . ' ' . $student->surname . ' (' . $student->class . '-' . $student->getStream->name . ')' }}
                                                                    </option>

                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="inputStatus3">Relationship with student </label>
                                                            <select required="" name="type" id="inputStatus"
                                                                class="form-control custom-select">
                                                                <option value="">Select one</option>
                                                                <option value="father">Father</option>
                                                                <option value="mother">Mother</option>
                                                                <option value="guardian">Guardian</option>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> --}}
    <!-- Page specific script -->

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

        // INITIALIZE DATEPICKER PLUGIN
        $('.datepicker').datepicker({
            clearBtn: true,
            format: "yyyy-mm-dd"
        });


        // FOR DEMO PURPOSE
        $('#reservationDate').on('change', function () {
            var pickedDate = $('input').val();
            $('#pickedDate').html(pickedDate);
        });
        });
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
