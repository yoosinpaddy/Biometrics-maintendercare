<?php

namespace App\Http\Controllers;

use App\Models\FaceRecord;
use App\Models\Guardian;
use App\Models\PremiumReports;
use App\Models\Smstemplete;
use App\Models\Staff;
use App\Models\StaffFaceRecord;
use App\Models\Stream;
use App\Models\Student;
use App\Models\SubscriptionReports;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Expr\Cast\Array_;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function home(Request $request)
    {
        $user = Auth::user();
        $new = 0;
        $inProgress = 0;
        $submited = 0;
        $complete = 0;

        $formated_classes = array();
        $streams = Stream::all();
        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        foreach ($classes as $class) {
            $myclass = [];
            $studentCount = 0;
            $studentCount = sizeof(Student::where('class', '=', $class->class)
                ->get());
            $myclass['class'] = $class->class;
            $myclass['stream'] = '(All)';
            $myclass['stream_id'] = 'all';
            $myclass['_count'] = $studentCount;
            array_push($formated_classes, $myclass);
            $studentCount = 0;
            foreach ($streams as $stream) {
                $studentCount = sizeof(Student::where('class', '=', $class->class)
                    ->where('stream', '=', $stream->id)
                    ->get());
                $myclass['class'] = $class->class;
                $myclass['stream'] = $stream->name;
                $myclass['stream_id'] = $stream->id;
                $myclass['_count'] = $studentCount;
                array_push($formated_classes, $myclass);
            }
        }
        // dd($streams);
        // $notifications=Notification::where('receiver_id','=',$user->id)->get();
        // $new=sizeof(Order::where(function($query) {
        //     return $query->where('status', '=', 'new')
        //         ->orWhere('status', '=', 'paid');
        // })->where('customer_id', '=', $user->id)->get());
        // $newOrders=Order::where('status', '=', 'new')->where('customer_id','=',$user->id)->get();
        // dd($newOrders);
        // $new=sizeof($newOrders);
        // $inProgress=sizeof(Order::where('customer_id', '=', $user->id)->where('status', '=', 'assigned')->where('status', '=', 'revision')->get());
        // $submited=sizeof(Order::where('customer_id', '=', $user->id)->where('status', '=', 'submitted')->get());
        // $complete=sizeof(Order::where('customer_id', '=', $user->id)->where('status', '=', 'completed')->get());
        return view('school.home', ['formated_classes' => $formated_classes, 'streams' => $streams]);
    }

    public function login(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('school.login', []);
        }
        $validate = $request->validate([
            'email' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
        ]);

        if (sizeof(User::where('email', '=', $request->email)->get()) > 0) {
            $user = User::where('email', '=', $request->email)->first();
            if ($user->password == null) {

                $user->password = Hash::make($request->password);
                $user->update();
            } else {
            }
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect(route('school.home'));
        }

        return back()->withErrors([
            'email' => 'Check the details and try again',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect(route('school.login'));
    }


    public function register(Request $request)
    {

        if ($request->method() == 'GET') {
            return view('school.register', []);
        }
        $validate = $request->validate([
            'name' => ['required', 'max:255'],
            'phone' => ['required', 'max:255'],
            'email' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
            'rpassword' => ['required', 'max:255'],
            'terms' => ['required', 'max:255'],
        ]);
        if ($request->password !== $request->rpassword) {
            $errors = array();
            $errors = ['Passwords do not match'];
            return redirect()->back()->withErrors($errors);
        }
        $user = new User();
        $exists = false;
        if (sizeof(User::where('email', '=', $request->email)->get()) > 0) {
            $user = User::where('email', '=', $request->email)->first();
            if ($user->password == null) {
            } else {
                $errors = array();
                $errors = ['Email already exists!'];
                return redirect()->back()->withErrors($errors);
            }
        }
        // dd($user);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        if ($user->save()) {

            if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                $request->session()->regenerate();

                return redirect(route('school.home'));
            }

            return back()->withErrors([
                'email' => 'Reg. successful try logging in',
            ]);
            return view('school.login', []);
        }
    }
    public function newParent(Request $request)
    {
        $validate = $request->validate([
            'student_id' => ['required', 'max:255'],
            'phone' => ['required', 'max:255'],
            'fname' => ['required', 'max:255'],
            'surname' => ['required', 'max:255'],
            'type' => ['required', 'max:255'], //father,mother,guardian
        ]);

        if (sizeof(Student::where('id', '=', $request->student_id)->get()) == 0) {
            return back()->withErrors([
                'student_id' => 'Student doesnot exist',
            ]);
        }

        $user = new User();
        if (sizeof(User::where('phone', '=', $request->phone)->get()) > 0) {
            $user = User::where('phone', '=', $request->phone)->limit(1)->get()->first();
        }
        $user->name = $request->fname . ' ' . $request->surname;
        $user->phone = $request->phone;
        $user->save();
        // dd($user->id);
        $guardian = new Guardian();
        $guardian->user_id = $user->id;
        $guardian->student_id = $request->student_id;
        $guardian->phone = $request->phone;
        $guardian->fname = $request->fname;
        $guardian->surname = $request->surname;
        $guardian->type = $request->type;
        if (sizeof(Guardian::where('student_id', '=', $request->student_id)->where('phone', '=', $request->phone)->get()) > 0) {
            return back()->with('success', 'Parent added successfully');
        }
        if ($guardian->save()) {
            return back()->with('success', 'Parent added successfully');
        }

        return back()->withErrors([
            'message' => 'Something went wrong, contact system administrator',
        ]);
    }
    public function newStaff(Request $request)
    {
        $validate = $request->validate([
            'staff_id' => ['required', 'max:255'],
            'phone' => ['required', 'max:255'],
            'fname' => ['required', 'max:255'],
            'surname' => ['required', 'max:255'],
            'type' => ['required', 'max:255'], //teaching non-teaching
        ]);


        $user = new User();
        if (sizeof(User::where('phone', '=', $request->phone)->get()) > 0) {
            $user = User::where('phone', '=', $request->phone)->first();
        }
        $user->name = $request->fname . ' ' . $request->surname;
        $user->phone = $request->phone;
        $user->save();
        // dd($user->id);
        $staff = new Staff();
        if (sizeof(Staff::where('phone', '=', $request->phone)->get()) > 0) {
            $staff = Staff::where('phone', '=', $request->phone)->first();
        }
        if (sizeof(Staff::where('staff_id', '=', $request->staff_id)->get()) > 0) {
            $staff = Staff::where('staff_id', '=', $request->staff_id)->first();
        }
        $staff->user_id = $user->id;
        $staff->staff_id = $request->staff_id;
        $staff->phone = $request->phone;
        $staff->fname = $request->fname;
        $staff->surname = $request->surname;
        $staff->type = $request->type;
        if (sizeof(Guardian::where('student_id', '=', $request->student_id)->where('phone', '=', $request->phone)->get()) > 0) {
            return back()->with('success', 'staff added successfully');
        }
        if ($staff->save()) {
            return back()->with('success', 'staff added successfully');
        }

        return back()->withErrors([
            'message' => 'Something went wrong, contact system administrator',
        ]);
    }

    public function newStudent(Request $request)
    {
        $validate = $request->validate([
            'class' => ['required', 'max:255'],
            'upi_no' => ['required', 'max:255'],
            'fname' => ['required', 'max:255'],
            'surname' => ['required', 'max:255'],
            'stream' => ['required', 'max:255'], //middle
        ]);
        if (sizeof(Student::where('upi_no', '=', $request->upi_no)->get()) > 0) {
            return back()->withErrors([
                'upi_no' => 'UPI already exists exist',
            ]);
        }

        $user = new User();
        $user->name = $request->fname . ' ' . $request->surname;
        $user->save();
        $student = new Student();
        $student->upi_no = $request->upi_no;
        $student->user_id = $user->id;
        $student->first_name = $request->fname;
        $student->surname = $request->surname;
        $student->class = $request->class;
        $student->class_year = date("Y");
        $student->stream = $request->stream;
        if ($request->middle_name != null || $request->middle_name != "") {
            $student->middle_name = $request->middle_name;
        }
        if ($student->save()) {
            return back()->with('success', 'Student added successfully');
        }
    }
    public function updateStudent(Request $request)
    {
        $validate = $request->validate([
            'class' => ['required', 'max:255'],
            'upi_no' => ['required', 'max:255'],
            'fname' => ['required', 'max:255'],
            'surname' => ['required', 'max:255'],
            'stream' => ['required', 'max:255'], //middle
            'student_id' => ['required', 'max:255'], //middle
        ]);
        if (sizeof(Student::where('id', '=', $request->student_id)->get()) == 0) {
            return back()->withErrors([
                'error' => 'student does not exist',
            ]);
        }
        $student = Student::where('id', '=', $request->student_id)->get()->first();


        if ($request->upi_no != $student->upi_no && sizeof(Student::where('upi_no', '=', $request->upi_no)->get()) > 0) {
            // dd($request->upi_no.$student->upi_no);
            return back()->withErrors([
                'upi_no' => 'UPI already exists exist',
            ]);
        }
        $user = $student->user;
        $user->name = $request->fname . ' ' . $request->surname;
        $user->save();

        $student->upi_no = $request->upi_no;
        $student->first_name = $request->fname;
        $student->surname = $request->surname;
        $student->class = $request->class;
        $student->stream = $request->stream;
        if ($request->middle_name != null || $request->middle_name != "") {
            $student->middle_name = $request->middle_name;
        }
        if ($student->save()) {
            return back()->with('success', 'Student Updated successfully');
        }
    }

    public function reportsPoster(Request $request)
    {
        $validate = $request->validate([
            'class' => ['required', 'max:255'],
            'stream' => ['required', 'max:255'],
            'day' => ['required', 'max:255'],
        ]);
        return redirect()->route('school.detailedReports', ['class' => $request->class, 'stream' => $request->stream, 'day' => $request->day]);
    }
    public function staffReportsPoster(Request $request)
    {
        $validate = $request->validate([
            'day' => ['required', 'max:255'],
        ]);
        return redirect()->route('staff.reports', ['day' => $request->day, 'type' => $request->type]);
    }

    public function streams(Request $request)
    {
        $streams = Stream::paginate(100);
        return view('school.streams', ['streams' => $streams]);
    }

    public function streamsNew(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'max:255'],
        ]);
        $stream = new Stream();
        $stream->name = $request->name;
        if ($stream->save()) {

            return back()->with('success', 'Added successfully');
        }
    }
    public function streamsUpdate(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'max:255'],
            'id' => ['required', 'max:255'],
        ]);
        $stream = Stream::where('id', '=', $request->id)->first();
        if ($stream == null) {
            return back()->withErrors([
                'error' => 'Something went wrong',
            ]);
        } else {
            $stream->name = $request->name;

            if ($stream->save()) {

                return back()->with('success', 'Added successfully');
            }
        }
    }
    public function studentsPoster(Request $request)
    {
        $validate = $request->validate([
            'class' => ['required', 'max:255'],
            'stream' => ['required', 'max:255'],
        ]);
        return redirect()->route('school.class.data', ['class_name' => $request->class, 'stream_id' => $request->stream]);
    }
    public function templetes(Request $request)
    {
        $templetes = Smstemplete::paginate(100);
        return view('school.templete', ['templetes' => $templetes]);
    }
    public function uploadCsv(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('school.upload', []);
        }
        $file = $request->file('csv');

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file size
        if ($fileSize <= $maxFileSize) {

            // File upload location
            $location = 'uploads';

            // Upload file
            $file->move($location, $filename);

            // Import CSV to Database
            $filepath = public_path($location . "/" . $filename);
            $path = getcwd(). "/".$location . "/" . $filename;
            $path2 = __DIR__.$location . "/" . $filename;
            // dd($filepath,$path,$path2);
            // Reading file
            $file = fopen($path, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);

                // Skip first row (Remove below comment if you want to skip the first row)
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);

            // Insert to MySQL database

            $json = json_encode($importData_arr);

            $errors = array();
            // Insert to MySQL database
            foreach ($importData_arr as $importData) {
                $upi = $this->getUpi(trim($importData[0]));
                $fname = trim($importData[1]);
                $v = explode(" ", trim($importData[2]));
                if (sizeof($v) == 2) {
                    $surname = $v[1];
                    $mname = $v[0];
                } else {
                    $surname = $v[0];
                    $mname = "";
                }
                $class = trim($importData[3]);
                $stream = trim($importData[4]);
                //process stream
                $rstream = $this->getMyStream($stream);
                //if student exists
                if (sizeof(Student::where('upi_no', '=', $upi)->get()) > 0) {
                    $student = Student::where('upi_no', '=', $upi)->limit(1)->get()->first();
                    $user = User::where('id', '=', $student->user_id)->limit(1)->get()->first();
                } else {
                    //insert student user
                    $user = new User();
                    $user->name = $fname . ' ' . $surname;
                    $user->save();
                    $student = new Student();
                }
                $student->upi_no = $upi;
                $student->user_id = $user->id;
                $student->first_name = $fname;
                $student->surname = $surname;
                $student->class = $class;
                $student->class_year = date("Y");
                $student->stream = $rstream;
                if ($mname != null || $mname != "") {
                    $student->middle_name = $mname;
                }
                $student->save();

                //if father is present
                if (trim($importData[5]) !== "") {
                    $ffname = trim($importData[5]);
                    $fsname = trim($importData[6]);
                    $fphone = $this->getPhone(trim($importData[7]));
                    //insert father user
                    $user2 = new User();
                    if (sizeof(User::where('phone', '=', $fphone)->get()) > 0) {
                        $user2 = User::where('phone', '=', $fphone)->limit(1)->first();
                    }
                    $user2->name = $ffname . ' ' . $fsname;
                    $user2->phone = $fphone;
                    $user2->save();
                    // dd($user->id);
                    //add to guardian table
                    $guardian = new Guardian();
                    if (sizeof(Guardian::where('user_id', '=', $user2->id)->get()) > 0) {
                        $guardian = Guardian::where('user_id', '=', $user2->id)->first();
                    }
                    $guardian->user_id = $user2->id;
                    $guardian->student_id =  $student->id;
                    $guardian->phone = $fphone;
                    $guardian->fname = $ffname;
                    $guardian->surname = $fsname;
                    $guardian->type = "father";
                    if (sizeof(Guardian::where('student_id', '=', $student->id)->where('phone', '=', $fphone)->get()) > 0) {
                        // return back()->with('success', 'Parent added successfully');
                    }
                    if ($guardian->save()) {
                        // return back()->with('success', 'Parent added successfully');
                    } else {
                        array_push($errors, 'father failed');
                    }
                }
                //if mother is present
                if (trim($importData[8]) !== "") {
                    $mfname = trim($importData[8]);
                    $msname = trim($importData[9]);
                    $mphone = $this->getPhone(trim($importData[10]));
                    //insert mother user
                    $user2 = new User();
                    if (sizeof(User::where('phone', '=', $mphone)->get()) > 0) {
                        $user2 = User::where('phone', '=', $mphone)->limit(1)->get()->first();
                    }
                    $user2->name = $mfname . ' ' . $msname;
                    $user2->phone = $mphone;
                    $user2->save();
                    // dd($user->id);
                    //add to guardian table
                    $guardian = new Guardian();
                    if (sizeof(Guardian::where('user_id', '=', $user2->id)->get()) > 0) {
                        $guardian = Guardian::where('user_id', '=', $user2->id)->first();
                    }
                    $guardian->user_id = $user2->id;
                    $guardian->student_id =  $student->id;
                    $guardian->phone = $mphone;
                    $guardian->fname = $mfname;
                    $guardian->surname = $msname;
                    $guardian->type = "mother";
                    if (sizeof(Guardian::where('student_id', '=', $student->id)->where('phone', '=', $mphone)->get()) > 0) {
                        // return back()->with('success', 'Parent added successfully');
                    }
                    if ($guardian->save()) {
                        // return back()->with('success', 'Parent added successfully');
                    } else {
                        array_push($errors, 'father failed');
                    }
                }
            }

            return "done";
            // return $json;
        } else {
            // Session::flash('message','File too large. File must be less than 2MB.');
        }
    }
    function getUpi($v)
    {

        if (str_split($v)[0] === "0"||sizeof(str_split($v))==5) {
            return $v;
        }else{
            return "0" . $v;
        }
    }
    function getPhone($v)
    {

        if (str_split($v)[0] === "7" || str_split($v)[0] === "1") {
            return "0" . $v;
        }
        return $v;
    }
    function getMyStream($request)
    {
        $stream = Stream::where('name', '=', $request)->first();
        if ($stream != null) {
            return $stream->id;
        }
        $stream = new Stream();
        $stream->name = $request;
        if ($stream->save()) {
            return $stream->id;
        }
        return 0;
    }

    public function templetesUpdate(Request $request)
    {
        $validate = $request->validate([
            'id' => ['required', 'max:255'],
        ]);

        $templetes = Smstemplete::where('id', '=', $request->id)->first();
        if ($request->content == null || $request->content == "") {
            $templetes->content = "";
        } else {
            $templetes->content = $request->content;
        }
        if ($templetes->save()) {
            return back()->with('success', 'Updated successfully');
        } else {
            return back()->withErrors([
                'error' => 'Something went wrong',
            ]);
        }
        return view('school.templete', ['templetes' => $templetes]);
    }
    public function staff(Request $request)
    {
        $staffRecords = array();
        $day = $request->day;
        $myvar = strtotime($day) * 1000;
        $myvar2 = strtotime('+24 hours', strtotime($day)) * 1000;

        $title = '';
        if ($request->type == 'teaching') {
            $title = 'Teaching';
            $staffRecords = StaffFaceRecord::whereNotNull('staff_face_records.status')->where('time_taken', '>', $myvar)
                ->where('time_taken', '<', $myvar2)
                ->where('staff_type', '=', 'teaching')
                ->orderBy('staff_face_records.created_at', 'ASC')->paginate(300);
        } else {
            $title = 'Teaching';
            $staffRecords = StaffFaceRecord::whereNotNull('staff_face_records.status')->where('time_taken', '>', $myvar)
                ->where('time_taken', '<', $myvar2)
                ->where('staff_type', '!=', 'teaching')
                ->orderBy('staff_face_records.created_at', 'ASC')->paginate(300);
        }

        return view('staff.reports', [
            'staffRecords' => $staffRecords,
            'title' => $title,
            'type' => $request->type,
            'day' => $day,
        ]);
    }
    public function reports(Request $request)
    {

        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $streams = Stream::all();

        // $validate = $request->validate([
        //     'class' => ['required', 'max:255'],
        //     'stream' => ['required', 'max:255'],
        // ]);
        // dd($request->stream);
        $allStudents = array();
        $title = '';
        $current_class = 'all';
        $current_stream = 'all';
        $current_streamv = 'all';
        $key1 = 2;
        $myRecords = array();
        // dd(sizeof($myRecords));
        // dd($myRecords);
        if ($request->class != 'all') {
            $current_class = $request->class;
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $title = 'Class ' . $request->class . '-' . $stream_name[0]->name;
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $allStudents = Student::where('class', '=', $request->class)->where('stream', '=', $request->stream)->paginate(300);

                $myRecords = FaceRecord::whereDate('face_records.created_at', Carbon::today())
                    ->join('students', function ($join) {
                        $join->on('students.upi_no', '=', 'face_records.upi_no');
                    })->where('students.class', '=', $request->class)->whereNotNull('face_records.status')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'Class ' . $request->class;
                $allStudents = Student::where('class', '=', $request->class)->paginate(300);
                $myRecords = FaceRecord::whereDate('face_records.created_at', Carbon::today())
                    ->join('students', function ($join) {
                        $join->on('students.upi_no', '=', 'face_records.upi_no');
                    })->whereNotNull('face_records.status')->where('students.class', '=', $request->class)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            }
        } else {
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $title = 'All Classes of Stream ' . $stream_name[0]->name;
                // dd($stream_name[0]->name);
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $allStudents = Student::where('stream', '=', $request->stream)->paginate(300);
                $myRecords = FaceRecord::whereDate('face_records.created_at', Carbon::today())
                    ->join('students', function ($join) {
                        $join->on('students.upi_no', '=', 'face_records.upi_no');
                    })->whereNotNull('face_records.status')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'All Classes in all Streams';
                $allStudents = Student::paginate(300);
                $myRecords = FaceRecord::whereDate('face_records.created_at', Carbon::today())->whereNotNull('face_records.status')->orderBy('face_records.created_at', 'ASC')->paginate(300);
            }
        }
        // dd($myRecords[0]->student);
        $parents = Guardian::paginate(100);
        //    dd($parents[0]->student);
        return view('school.reports', [
            'parents' => $parents, 'allStudents' => $allStudents,
            'classes' => $classes, 'streams' => $streams, 'title' => $title,
            'myRecords' => $myRecords,
            'current_class' => $current_class,
            'current_stream' => $current_stream,
            'current_streamv' => $current_streamv,
        ]);
    }
    public function detailedReports(Request $request)
    {

        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $streams = Stream::all();


        $allStudents = array();
        $title = '';
        $current_class = 'all';
        $current_stream = 'all';
        $current_streamv = 'all';
        $day = $request->day;
        $myvar = strtotime($day) * 1000;
        $myvar2 = strtotime('+24 hours', strtotime($day)) * 1000;
        $key1 = 2;
        $myRecords = array();
        // dd(sizeof($myRecords));
        // dd($myRecords);
        if ($request->class != 'all') {
            $current_class = $request->class;
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $title = 'Class ' . $request->class . '-' . $stream_name[0]->name;
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $allStudents = Student::where('class', '=', $request->class)->where('stream', '=', $request->stream)->paginate(300);

                $myRecords = FaceRecord::join('students', function ($join) {
                    $join->on('students.upi_no', '=', 'face_records.upi_no');
                })->where('time_taken', '>', $myvar)
                    ->where('time_taken', '<', $myvar2)
                    ->where('students.class', '=', $request->class)->whereNotNull('face_records.status')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'Class ' . $request->class;
                $allStudents = Student::where('class', '=', $request->class)->paginate(300);
                $myRecords = FaceRecord::join('students', function ($join) {
                    $join->on('students.upi_no', '=', 'face_records.upi_no');
                })->where('time_taken', '>', $myvar)
                    ->where('time_taken', '<', $myvar2)
                    ->whereNotNull('face_records.status')->where('students.class', '=', $request->class)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            }
        } else {
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $title = 'All Classes of Stream ' . $stream_name[0]->name;
                // dd($stream_name[0]->name);
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $allStudents = Student::where('stream', '=', $request->stream)->paginate(300);
                $myRecords = FaceRecord::join('students', function ($join) {
                    $join->on('students.upi_no', '=', 'face_records.upi_no');
                })->where('time_taken', '>', $myvar)
                    ->where('time_taken', '<', $myvar2)
                    ->whereNotNull('face_records.status')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'All Classes in all Streams';
                $allStudents = Student::paginate(300);
                $myRecords = FaceRecord::whereNotNull('face_records.status')->where('time_taken', '>', $myvar)
                    ->where('time_taken', '<', $myvar2)
                    ->orderBy('face_records.created_at', 'ASC')->paginate(300);
            }
        }
        // dd($myRecords[0]->student);
        $parents = Guardian::paginate(100);
        //    dd($parents[0]->student);
        return view('school.reports', [
            'parents' => $parents, 'allStudents' => $allStudents,
            'classes' => $classes, 'streams' => $streams, 'title' => $title,
            'myRecords' => $myRecords,
            'current_class' => $current_class,
            'current_stream' => $current_stream,
            'current_streamv' => $current_streamv,
            'day' => $day,
        ]);
    }
    public function delivery_reports_sms(Request $request)
    {
        $subscribers=sizeof(SubscriptionReports::where('updateType','!=','deletion')->get());
        $successful=sizeof(PremiumReports::where('status','=','Success')->get());
        $insufficient=sizeof(PremiumReports::where('failureReason','like','%InsufficientCredit%')->get());

        $reports=PremiumReports::where('status','=','Success')
            ->orWhere('status','=','Failed')
            ->orderBy('updated_at', 'DESC')
            ->paginate(50);

        return view('sms.premiumCallback', [
            'successful' => $successful,
            'subscribers' => $subscribers,
            'insufficient' => $insufficient,
            'reports' => $reports,
            'title'=>'Premium Sms Reports'
        ]);

//        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
//        $streams = Stream::all();
//
//
//        $allStudents = array();
//        $title = '';
//        $current_class = 'all';
//        $current_stream = 'all';
//        $current_streamv = 'all';
//        $day = $request->day;
//        $myvar = strtotime($day) * 1000;
//        $myvar2 = strtotime('+24 hours', strtotime($day)) * 1000;
//        $key1 = 2;
//        $myRecords = array();
//        // dd(sizeof($myRecords));
//        // dd($myRecords);
//        if ($request->class != 'all') {
//            $current_class = $request->class;
//            if ($request->stream != 'all') {
//                $stream_name = Stream::where('id', '=', $request->stream)->get();
//                $title = 'Class ' . $request->class . '-' . $stream_name[0]->name;
//                $current_stream = $stream_name[0]->name;
//                $current_streamv = $request->stream;
//                $allStudents = Student::where('class', '=', $request->class)->where('stream', '=', $request->stream)->paginate(300);
//
//                $myRecords = FaceRecord::join('students', function ($join) {
//                    $join->on('students.upi_no', '=', 'face_records.upi_no');
//                })->where('time_taken', '>', $myvar)
//                    ->where('time_taken', '<', $myvar2)
//                    ->where('students.class', '=', $request->class)->whereNotNull('face_records.status')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
//            } else {
//                $title = 'Class ' . $request->class;
//                $allStudents = Student::where('class', '=', $request->class)->paginate(300);
//                $myRecords = FaceRecord::join('students', function ($join) {
//                    $join->on('students.upi_no', '=', 'face_records.upi_no');
//                })->where('time_taken', '>', $myvar)
//                    ->where('time_taken', '<', $myvar2)
//                    ->whereNotNull('face_records.status')->where('students.class', '=', $request->class)->orderBy('face_records.created_at', 'ASC')->paginate(300);
//            }
//        } else {
//            if ($request->stream != 'all') {
//                $stream_name = Stream::where('id', '=', $request->stream)->get();
//                $title = 'All Classes of Stream ' . $stream_name[0]->name;
//                // dd($stream_name[0]->name);
//                $current_stream = $stream_name[0]->name;
//                $current_streamv = $request->stream;
//                $allStudents = Student::where('stream', '=', $request->stream)->paginate(300);
//                $myRecords = FaceRecord::join('students', function ($join) {
//                    $join->on('students.upi_no', '=', 'face_records.upi_no');
//                })->where('time_taken', '>', $myvar)
//                    ->where('time_taken', '<', $myvar2)
//                    ->whereNotNull('face_records.status')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
//            } else {
//                $title = 'All Classes in all Streams';
//                $allStudents = Student::paginate(300);
//                $myRecords = FaceRecord::whereNotNull('face_records.status')->where('time_taken', '>', $myvar)
//                    ->where('time_taken', '<', $myvar2)
//                    ->orderBy('face_records.created_at', 'ASC')->paginate(300);
//            }
//        }
//        // dd($myRecords[0]->student);
//        $parents = Guardian::paginate(100);
//        //    dd($parents[0]->student);
//        return view('sms.premiumCallback',['']);
//        return view('school.reports', [
//            'parents' => $parents, 'allStudents' => $allStudents,
//            'classes' => $classes, 'streams' => $streams, 'title' => $title,
//            'myRecords' => $myRecords,
//            'current_class' => $current_class,
//            'current_stream' => $current_stream,
//            'current_streamv' => $current_streamv,
//            'day' => $day,
//        ]);
    }
    public function send_bulk_sms(Request $request)
    {

        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $streams = Stream::all();

        $validate = $request->validate([
            'class' => ['required', 'max:255'],
            'stream' => ['required', 'max:255'],
            'message' => ['required', 'max:255'],
        ]);
        // dd($request->stream);
        $title = '';
        $current_class = 'all';
        $current_stream = 'all';
        $current_streamv = 'all';
        $key1 = 2;
        $myParents = array();
        // dd(sizeof($myRecords));
        // dd($myRecords);
        if ($request->class != 'all') {
            $current_class = $request->class;
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $title = 'Class ' . $request->class . '-' . $stream_name[0]->name;
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $myParents = Guardian::join('students', function ($join) {
                    $join->on('students.id', '=', 'guardians.student_id');
                })->where('students.class', '=', $request->class)->where('students.stream', '=', $request->stream)->get();
            } else {
                $title = 'Class ' . $request->class;
                $myParents = Guardian::join('students', function ($join) {
                    $join->on('students.id', '=', 'guardians.student_id');
                })->where('students.class', '=', $request->class)->get();
            }
        } else {
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $title = 'All Classes of Stream ' . $stream_name[0]->name;
                // dd($stream_name[0]->name);
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $myParents = Guardian::join('students', function ($join) {
                    $join->on('students.id', '=', 'guardians.student_id');
                })->where('students.stream', '=', $request->stream)->get();
            } else {
                $title = 'All Classes in all Streams';
                $myParents = Guardian::all();
            }
        }

        foreach ($myParents as $parent) {
            $this->sendSms($parent, $request->message);
        }
        return back()->with('success', 'Messages sent successfully');
    }
    public function bulk_sms(Request $request)
    {
        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $streams = Stream::all();
        return view('school.sendSms', ['classes' => $classes, 'streams' => $streams,]);
    }

    public function classReports(Request $request)
    {

        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $streams = Stream::all();

        // $validate = $request->validate([
        //     'class' => ['required', 'max:255'],
        //     'stream' => ['required', 'max:255'],
        // ]);
        // dd($request->stream);
        $allStudents = array();
        $title = '';
        $current_class = 'all';
        $current_stream = 'all';
        $current_streamv = 'all';
        if ($request->class != 'all') {
            $current_class = $request->class;
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $title = 'Class ' . $request->class . '-' . $stream_name[0]->name;
                $allStudents = Student::where('class', '=', $request->class)->where('stream', '=', $request->stream)->paginate(300);
            } else {
                $title = 'Class ' . $request->class;
                $allStudents = Student::where('class', '=', $request->class)->paginate(300);
            }
        } else {
            if ($request->stream != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream)->get();
                // dd($stream_name[0]->name);
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream;
                $title = 'All Classes of Stream ' . $stream_name[0]->name;
                $allStudents = Student::where('stream', '=', $request->stream)->paginate(300);
            } else {
                $title = 'All Classes in all Streams';
                $allStudents = Student::paginate(300);
            }
        }
        $parents = Guardian::paginate(100);
        //    dd($parents[0]->student);
        return view('school.reports', [
            'parents' => $parents, 'allStudents' => $allStudents,
            'classes' => $classes, 'streams' => $streams, 'title' => $title,
            'current_class' => $current_class,
            'current_stream' => $current_stream,
            'current_streamv' => $current_streamv,
        ]);
    }
    public function getParents(Request $request)
    {
        $parents = Guardian::orderby('fname')->paginate(100);
        $allStudents = Student::orderby('first_name')->get();
        //    dd($parents[0]->student);
        return view('school.parents', ['parents' => $parents, 'allStudents' => $allStudents]);
    }
    public function myClass(Request $request)
    {
        // $allStudents = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $allStudents = array();
        $classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $title = '';
        $streams = Stream::all();
        $current_class = 'all';
        $current_stream = 'all';
        $current_streamv = 'all';
        // dd(sizeof($myRecords));
        // dd($myRecords);
        if ($request->class_name != 'all') {
            $current_class = $request->class_name;
            if ($request->stream_id != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream_id)->get();
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream_id;
                $title = 'Class ' . $request->class . '-' . $stream_name[0]->name;
                $allStudents = Student::where('class', '=', $request->class_name)->where('stream', '=', $request->stream_id)->where('class', '!=', '9')->paginate(300);
            } else {
                $title = 'Class ' . $request->class;
                $allStudents = Student::where('class', '=', $request->class_name)->paginate(300);
            }
        } else {
            if ($request->stream_id != 'all') {
                $stream_name = Stream::where('id', '=', $request->stream_id)->get();
                $current_stream = $stream_name[0]->name;
                $current_streamv = $request->stream_id;
                $title = 'All Classes of Stream ' . $stream_name[0]->name;
                $allStudents = Student::where('stream', '=', $request->stream_id)->where('class', '!=', '9')->paginate(300);
            } else {
                $title = 'All Classes in all Streams';
                $allStudents = Student::where('class', '!=', '9')->paginate(300);
            }
        }
        // dd($allStudents[1]->getStream);
        return view('school.students', [
            'allStudents' => $allStudents,
            'current_class' => $current_class,
            'current_stream' => $current_stream,
            'current_streamv' => $current_streamv,
            'classes' => $classes,
            'streams' => $streams, 'title' => $title
        ]);
    }
    public function delete(Request $request)
    {
        $guardian = Guardian::where('id', '=', $request->student_id)->first();
        if ($guardian->delete()) {
            return back()->with('success', 'Parent deleted successfully');
        }
        return back()->withErrors([
            'message' => 'Something went wrong, contact system administrator',
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
    public function sendSms($guardian, $message)
    {
//        if($guardian->id<524){
//            return;
//        }

        $response = Http::asForm()->withHeaders([
            'apikey' => $_ENV['SMS_NORMAL_API_KEY'],
        ])->post('https://api.africastalking.com/version1/messaging', [
            'username' => $_ENV['SMS_NORMAL_USERNAME'],
            'from' => $_ENV['SMS_FROM'],
            'message' => $message,
            'to' => $guardian->phone,
        ]);
        if ($response->successful()) {
            // dd($response->json()['responses'][0]['response-description']);
            return back()->with('success', 'Message sent successfully');
        }

        // Determine if the status code is >= 400...
        if ($response->failed()) {
            // dd($response->json()['errors']['message'][0]);
            return back()->withErrors([
                'message' => $response->body(),
            ]);
        }

        // Determine if the response has a 400 level status code...
        if ($response->clientError()) {

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

        // Determine if the response has a 500 level status code...
        if ($response->serverError()) {

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }
    }

    public function trySms()
    {
        // dd($guardian->phone);
        $phone="0726569597";
        $message="ONLINE TEST";
        $response = Http::asForm()->withHeaders([
            'apikey' => $_ENV['SMS_NORMAL_API_KEY'],
        ])->post('https://api.africastalking.com/version1/messaging', [
            'username' => $_ENV['SMS_NORMAL_USERNAME'],
            'from' => $_ENV['SMS_FROM'],
            'message' => $message,
            'to' => $phone,
        ]);
        if ($response->successful()) {
             dd($response->body());
            return back()->with('success', 'Message sent successfully');
        }

        // Determine if the status code is >= 400...
        if ($response->failed()) {
            // dd($response->json()['errors']['message'][0]);
            return back()->withErrors([
                'message' => $response->body(),
            ]);
        }

        // Determine if the response has a 400 level status code...
        if ($response->clientError()) {

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

        // Determine if the response has a 500 level status code...
        if ($response->serverError()) {

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }
    }
}
