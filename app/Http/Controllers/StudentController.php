<?php

namespace App\Http\Controllers;

use App\Models\FaceRecord;
use App\Models\Guardian;
use App\Models\Smstemplete;
use App\Models\Staff;
use App\Models\StaffFaceRecord;
use App\Models\Stream;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
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

        if (sizeof(User::where('id', '=', $request->student_id)->get()) == 0) {
            return back()->withErrors([
                'student_id' => 'Student doesnot exist',
            ]);
        }

        $user = new User();
        if (sizeof(User::where('phone', '=', $request->phone_number)->get()) > 0) {
            $user = User::where('phone', '=', $request->phone_number)->limit(1)->get()->first();
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
        if (sizeof(User::where('phone', '=', $request->phone_number)->get()) > 0) {
            $user = User::where('phone', '=', $request->phone_number)->limit(1)->get()->first();
        }
        $user->name = $request->fname . ' ' . $request->surname;
        $user->phone = $request->phone;
        $user->save();
        // dd($user->id);
        $staff = new Staff();
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


        if ($request->upi_no!=$student->upi_no&&sizeof(Student::where('upi_no', '=', $request->upi_no)->get()) > 0) {
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
        return redirect()->route('school.detailedReports', ['class' => $request->class, 'stream' => $request->stream,'day'=>$request->day]);
    }
    public function staffReportsPoster(Request $request)
    {
        $validate = $request->validate([
            'day' => ['required', 'max:255'],
        ]);
        return redirect()->route('staff.reports', ['day'=>$request->day,'type'=>$request->type]);
    }

    public function streams(Request $request)
    {
        $streams=Stream::paginate(100);
        return view('school.streams', ['streams' => $streams]);
    }

    public function streamsNew(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'max:255'],
        ]);
        $stream=new Stream();
$stream->name=$request->name;
if($stream->save()){

    return back()->with('success', 'Added successfully');
}
    }
    public function streamsUpdate(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'max:255'],
            'id' => ['required', 'max:255'],
        ]);
        $stream=Stream::where('id','=',$request->id)->first();
        if($stream==null){
            return back()->withErrors([
            'error' => 'Something went wrong',
        ]);

        }else{
$stream->name=$request->name;

if($stream->save()){

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
        $templetes=Smstemplete::paginate(100);
        return view('school.templete', ['templetes' => $templetes]);
    }
    public function templetesUpdate(Request $request)
    {
        $validate = $request->validate([
            'id' => ['required', 'max:255'],
        ]);

        $templetes=Smstemplete::where('id','=',$request->id)->first();
        if($request->content==null||$request->content==""){
            $templetes->content="";
        }else{
            $templetes->content=$request->content;
        }
if($templetes->save()){
    return back()->with('success', 'Updated successfully');

}else{
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
        if($request->type=='teaching'){
            $title = 'Teaching';
            $staffRecords = StaffFaceRecord::where('staff_face_records.status','=','enter')->where('time_taken','>',$myvar)
            ->where('time_taken','<',$myvar2)
            ->where('staff_type','=','teaching')
            ->orderBy('staff_face_records.created_at', 'ASC')->paginate(300);
        }else{
            $title = 'Teaching';
            $staffRecords = StaffFaceRecord::where('staff_face_records.status','=','enter')->where('time_taken','>',$myvar)
            ->where('time_taken','<',$myvar2)
            ->where('staff_type','!=','teaching')
            ->orderBy('staff_face_records.created_at', 'ASC')->paginate(300);

        }

        return view('staff.reports', [ 'staffRecords' => $staffRecords,
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
                    })->where('students.class', '=', $request->class)->where('face_records.status','=','enter')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'Class ' . $request->class;
                $allStudents = Student::where('class', '=', $request->class)->paginate(300);
                $myRecords = FaceRecord::whereDate('face_records.created_at', Carbon::today())
                    ->join('students', function ($join) {
                        $join->on('students.upi_no', '=', 'face_records.upi_no');
                    })->where('face_records.status','=','enter')->where('students.class', '=', $request->class)->orderBy('face_records.created_at', 'ASC')->paginate(300);
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
                    })->where('face_records.status','=','enter')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'All Classes in all Streams';
                $allStudents = Student::paginate(300);
                $myRecords = FaceRecord::whereDate('face_records.created_at', Carbon::today())->where('face_records.status','=','enter')->orderBy('face_records.created_at', 'ASC')->paginate(300);
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
                    })->where('time_taken','>',$myvar)
                    ->where('time_taken','<',$myvar2)
                    ->where('students.class', '=', $request->class)->where('face_records.status','=','enter')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'Class ' . $request->class;
                $allStudents = Student::where('class', '=', $request->class)->paginate(300);
                $myRecords = FaceRecord::join('students', function ($join) {
                        $join->on('students.upi_no', '=', 'face_records.upi_no');
                    })->where('time_taken','>',$myvar)
                    ->where('time_taken','<',$myvar2)
                    ->where('face_records.status','=','enter')->where('students.class', '=', $request->class)->orderBy('face_records.created_at', 'ASC')->paginate(300);
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
                    })->where('time_taken','>',$myvar)
                    ->where('time_taken','<',$myvar2)
                    ->where('face_records.status','=','enter')->where('students.stream', '=', $request->stream)->orderBy('face_records.created_at', 'ASC')->paginate(300);
            } else {
                $title = 'All Classes in all Streams';
                $allStudents = Student::paginate(300);
                $myRecords = FaceRecord::where('face_records.status','=','enter')->where('time_taken','>',$myvar)
                ->where('time_taken','<',$myvar2)
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
    {$classes = Student::select('class')->where('class', '!=', '9')->groupBy('class')->get();
        $streams = Stream::all();
        return view('school.sendSms',['classes' => $classes, 'streams' => $streams,]);
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
        $parents = Guardian::paginate(100);
        $allStudents = Student::all();
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
        return view('school.students', ['allStudents' => $allStudents,
        'current_class' => $current_class,
        'current_stream' => $current_stream,
        'current_streamv' => $current_streamv,
        'classes' => $classes,
        'streams' => $streams,'title'=>$title]);
    }
    public function delete(Request $request)
    {
        $guardian=Guardian::where('id','=',$request->student_id)->first();
        if($guardian->delete()){
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
    public function sendSms($guardian,$message){
// dd($guardian->phone);
        $response=Http::asForm()->post('https://quicksms.advantasms.com/api/services/sendsms',[
            'apikey'=>$_ENV['SMS_API_KEY'],
            'partnerID'=>$_ENV['SMS_PATNER_ID'],
            'shortcode'=>$_ENV['SMS_SHORT_CODE'],
            'message'=>$message,
            'mobile'=>$guardian->phone,
        ]);
        if($response->successful()){
            // dd($response->json()['responses'][0]['response-description']);
            return back()->with('success', $response->json()['responses'][0]['response-description']);
        }

        // Determine if the status code is >= 400...
        if($response->failed()){
// dd($response->json()['errors']['message'][0]);
            return back()->withErrors([
                'message' => $response->json()['errors']['message'][0],
            ]);
        }

        // Determine if the response has a 400 level status code...
        if($response->clientError()){

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

        // Determine if the response has a 500 level status code...
        if($response->serverError()){

            return back()->withErrors([
                'message' => 'Something went wrong, could not send sms',
            ]);
        }

    }
}
