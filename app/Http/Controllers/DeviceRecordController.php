<?php

namespace App\Http\Controllers;

use App\Models\DeviceRecord;
use App\Models\FaceRecord;
use App\Models\Guardian;
use App\Models\PremiumReports;
use App\Models\SmsRecord;
use App\Models\Smstemplete;
use App\Models\Staff;
use App\Models\StaffFaceRecord;
use App\Models\Student;
use App\Models\SubscriptionReports;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeviceRecordController extends Controller
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $school="device ".$request->school_id;
        if($request->school_id==1){
            $school=" 1";
        }else if($request->school_id==2){
            $school=" 2";
        }else if($request->school_id==3){
            $school=" 3";
        }else if($request->school_id==4){
            $school=" 4";
        }
        $record = new DeviceRecord();
        $record->data = 'HeartBeat|'.env('APP_NAME').$school.'|' . implode("|", $request->all());
        $record->save();
        return json_encode([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => (time() * 1000)
        ]);
    }

    public function updates(Request $request)
    {
//        dd(ini_get('max_execution_time'));
        $tester=0;
        while($tester>-1){
            $tester++;
        }
        dd(ini_get('max_execution_time'));
        global $level;
        $records = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
            ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
            //TODO delete this later
            ->where('status', '=', 'enter')
            ->select('upi_no','id')->distinct()
            ->orderBy('id','ASC')
            ->get();
        //        dd($records);
        foreach ($records as $key) {
            $enter = sizeof(FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                ->where('status', '=', 'enter')->where('upi_no', '=', $key->upi_no)
                ->get());
            $exit = sizeof(FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                ->where('status', '=', 'exit')->where('upi_no', '=', $key->upi_no)
                ->get());
            $mnull = sizeof(FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                ->whereNull('status')->where('upi_no', '=', $key->upi_no)
                ->get());

            if ($key->upi_no == "9999") {
                $level = $level . "\nFound him now top";
                //                dd($enter, $exit, $mnull, FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                //                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                //                    ->where('upi_no', '=', $key->upi_no)
                //                    ->get());

            }
            if ($enter == 0 && $exit == 0 && $mnull > 1) {
                $r = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                    ->whereNull('status')->where('upi_no', '=', $key->upi_no)
                    ->get()->first();
                if ($r) {
                    $r->status = 'enter';
                    $r->save();
                    $x = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                        ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                        ->whereNull('status')->where('upi_no', '=', $key->upi_no)
                        ->get()->first();
                    if ($x) {
                        $r->status = 'exit';
                        $r->save();
                    }
                }
            } else if ($enter == 0 && $exit == 0 && $mnull == 1) {
                $r = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                    ->whereNull('status')->where('upi_no', '=', $key->upi_no)
                    ->get()->first();
                if ($r) {
                    $r->status = 'enter';
                    $r->save();
                }
            } else if ($enter == 1) {
                //TODO only today
//                Send sms to parents
                $upi_no = $key->upi_no;
                $student = Student::where('upi_no', '=', $upi_no)->get()->first();
                // dd($student->id);
                if ($student != null) {

                    $level = $level . "\nisStudent";
                    $faceRecord = new FaceRecord();
                    $faceRecord->upi_no = $upi_no;


                    $guardian = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->first();
                    if ($guardian != null) {
                        $level = $level . "\nhasGuardian";
                        $faceR = FaceRecord::where('upi_no', '=', $upi_no)
                            ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                            ->where('time_taken', '<', (string)Carbon::today()->valueOf())
                            ->where('status', '=', 'enter')
                            ->orderby('id', 'DESC')
                            ->first();
                        // dd($faceR);
                        if ($faceR != null) {
                            $level = $level . "\nhasPrevFace:".$student->id;

                            $guardians = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->get();
                            foreach ($guardians as $key2) {
                                $level = $level . "\nFor each:".$key2->phone;
                                if ($faceR->upi_no == "9999") {
                                    $level = $level . "\nFound him now";
                                    $this->sendPremiumSms($key2, $faceR, $faceR->time_taken, 'first');

                                } else {

                                }
                            }
                        }else{
                            $level = $level . "\nnoPrevFace";

                        }

                        // return back()->with('success', 'Sms sent successfully');
                    }else{
                        $level = $level . "\nnoGuardian";

                    }
                }


                /////////////////////////
            } else if ($enter == 1 && $exit == 0 && $mnull == 1) {
                $r = FaceRecord::where('time_taken', '=', (string)Carbon::today()->valueOf())
                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                    ->whereNull('status')->where('upi_no', '=', $key->upi_no)
                    ->get()->first();
                if ($r) {
                    $r->status = 'exit';
                    $r->save();
                }
            } else if ($enter == 0 && $exit == 1) {
                $r = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                    ->where('upi_no', '=', $key->upi_no)
                    ->get()->first();
                if ($r) {
                    $r->status = 'enter';
                    $r->save();
                }
                $t = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                    ->where('status', '=', 'exit')->where('upi_no', '=', $key->upi_no)
                    ->get()->first();

                if (!$t) {
                    $y = FaceRecord::where('time_taken', '>', (string)Carbon::today()->valueOf())
                        ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                        ->whereNull('status')->where('upi_no', '=', $key->upi_no)
                        ->get()->first();
                    if ($y) {
                        $y->status = 'exit';
                        $y->save();
                    }
                }
            }
        }
        dd('done' . $level);
    }

    public $level = "Start---\n";

    public function recordUpload(Request $request)
    {
        $data = json_decode($request->data, TRUE);
        // dd($data['eno']);
        $coointer = 0;
        foreach ($data as $key1) {
            $coointer++;
            $this->loopUpload($key1, $request->all());
        }
        $record = new DeviceRecord();
        $record->data = 'recordUpload|ALl data:' . $coointer;
        $record->save();
        $myData = [
            'openDoor' => 1, //Whether open relay, 0: no, 1: open
            'tipSpeech' => "Thanks for verifying", //Display and voice over content It can be used \n to indicate a line swap, such as: "Zhang San Hello this consumption of $20 \n balance of $800."
            'state' => 2, //0: Display text and broadcast voice at the same time
            //1: Only text is displayed, no voice is broadcasted.
            //2: Do not display text, only voice
            'openDoor' => 1, //Whether
        ];
        $myResponse = json_encode([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => $myData,
            'stage' => $GLOBALS['level']
        ]);

        return response($myResponse)
            ->header('Content-Type', 'application/json');
    }

    public function loopUpload($data, $request)
    {
        global $level;
        $level = "Start---\n";
        $upi_no = $data['eno'];
        $time_taken = $data['scandatetime'];
        $device_serial = $data['macno'];
        $temperature = $data['temperature'];
        $event = $data['operatorno']; //operatorno Punch Type :
        // face_0: successful face recognition
        // face_2: stranger brushes face
        // card_0: swipe card successfully
        // card_2: Invalid card
        // idcard_0: Witness matching successful
        // idcard_2: witness match failure
        // faceAndcard_0: successful card+face dual authentication
        // faceAndcard_2: card+face dual authentication failure
        // open_0: button to open the door.
        // qrcode_0: QR Code Success
        // qrcode_2: unregistered QR code (no counterpart for QR code)
        // password_0: password to open the door
        // -2020-02-25: Adding a record of failed witness comparison
        // --2020-09-14 (version 2.2.2): add open door password

        if ($event == 'face_0' || $event == 'card_0' || $event == 'faceAndcard_0') {
            $student = Student::where('upi_no', '=', $upi_no)->get()->first();
            // dd($student->id);
            if ($student != null) {

                $level = $level . "\nisStudent";
                $faceRecord = new FaceRecord();
                $faceRecord->upi_no = $upi_no;
                $faceRecord->time_taken = $time_taken;
                $faceRecord->device_serial = $device_serial;
                $faceRecord->event = $event;
                $faceRecord->temperature = $temperature;


                $guardian = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->first();
                if ($guardian != null) {
                    $level = $level . "\nhasGuardian";
                    $faceR = FaceRecord::where('upi_no', '=', $upi_no)
                        ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                        ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                        ->orderby('id', 'DESC')
                        ->first();
                    // dd($faceR);
                    if ($faceR != null) {
                        $level = $level . "\nhasPrevFace";
                        //we have a record
                        //check if a record is already present within the past 30 minutes
                        $input = $faceR->time_taken;
                        $input2 = $time_taken;
                        $input = floor($input / 1000 / 60);
                        $input2 = floor($input2 / 1000 / 60);
                        if ($input2 - $input < 210) {
                            $level = $level . "\nisSlessThan1Minute";

                            // dd('<10');
                            //recent record taken
                            //Ignore
                            // $faceRecord->save();
                            // $this->sendSms($guardian,$faceRecord,$time_taken,'second');
                        } else {
                            $level = $level . "\nisgreaterThan1Minute";
                            //check if its the second record

                            if (sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                    ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                                    ->get()) == 1) {
                                $level = $level . "\nisExit";
                                // dd('second');
                                $faceRecord->status = 'exit';
                                $faceRecord->has_parent = 'yes';
                                $faceRecord->save();
                                //disable sms
//                                $guardians = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->get();
//                                foreach ($guardians as $key) {
//                                    $this->sendPremiumSms($key, $faceRecord, $time_taken, 'second');
//                                }
                                //send to one guardian
                                $guardian = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->get()->first();
                                $this->sendSms($guardian, $faceRecord, $time_taken, 'second',$student);
                            } else {
                                $level = $level . "\nisMore than 2 times" . sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                        ->whereDate('created_at', Carbon::today())
                                        ->get());
                                // dd(sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                // ->whereDate('created_at', Carbon::today())
                                // ->get()));

                                $faceRecord->has_parent = 'yes';
                                $faceRecord->save();
                            }
                        }
                    } else {
                        $level = $level . "\nnoFace";
                        //no record
                        // dd('first');
                        $faceRecord->status = 'exit';

                        $faceRecord->has_parent = 'yes';
                        $faceRecord->save();
                        //disable sms $guardians = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->get();
//
//                        $guardians = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->get();
//                        foreach ($guardians as $key) {
//                            $this->sendPremiumSms($key, $faceRecord, $time_taken, 'first');
//                        }
                        //Send 1 sms
                        $guardian = Guardian::where('student_id', '=', $student->id)->where('should_notify', '=', 'true')->get()->first();

                            $this->sendSms($guardian, $faceRecord, $time_taken, 'first',$student);

                    }

                    // return back()->with('success', 'Sms sent successfully');
                } else {
                    $level = $level . "\nnoGuardian";

                    $faceR = FaceRecord::where('upi_no', '=', $upi_no)
                        ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                        ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                        ->orderby('id', 'DESC')
                        ->first();
                    // dd($faceR);
                    if ($faceR != null) {
                        $level = $level . "\nhasPrevFace";
                        //we have a record
                        //check if a record is already present within the past 30 minutes
                        $input = $faceR->time_taken;
                        $input2 = $time_taken;
                        $input = floor($input / 1000 / 60);
                        $input2 = floor($input2 / 1000 / 60);
                        if ($input2 - $input < 210) {
                            $level = $level . "\nisSlessThan1Minute";

                            // dd('<10');
                            //recent record taken
                            //Ignore
                            // $faceRecord->save();
                            // $this->sendSms($guardian,$faceRecord,$time_taken,'second');
                        } else {
                            $level = $level . "\nisgreaterThan1Minute";
                            //check if its the second record

                            if (sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                    ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                                    ->get()) == 1) {
                                $level = $level . "\nisExit";
                                // dd('second');
                                $faceRecord->status = 'exit';

                                $faceRecord->has_parent = 'no';
                                $faceRecord->save();
                            } else {
                                $level = $level . "\nisMore than 2 times" . sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                        ->whereDate('created_at', Carbon::today())
                                        ->get());
                                // dd(sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                // ->whereDate('created_at', Carbon::today())
                                // ->get()));
                                $faceRecord->has_parent = 'no';
                                $faceRecord->save();
                            }
                        }
                    } else {
                        $level = $level . "\nnoFace";
                        //no record
                        // dd('first');
                        $faceRecord->status = 'exit';
                        $faceRecord->has_parent = 'no';
                        $faceRecord->save();
                    }
                }
            } else {
                $level = $level . "\nisStaff";
                $staff = Staff::where('staff_id', '=', $upi_no)->get()->first();
                if ($staff != null) {
                    $faceRecord = new StaffFaceRecord();
                    $faceRecord->reg_no = $upi_no;
                    $faceRecord->time_taken = $time_taken;
                    $faceRecord->device_serial = $device_serial;
                    $faceRecord->staff_type = $staff->type;
                    $faceRecord->event = $event;
                    $faceRecord->temperature = $temperature;


                    $faceR = StaffFaceRecord::where('reg_no', '=', $upi_no)
                        ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                        ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                        ->orderby('id', 'DESC')
                        ->first();
                    // dd($faceR);
                    if ($faceR != null) {
                        //we have a record
                        //check if a record is already present within the past 30 minutes
                        $input = $faceR->time_taken;
                        $input2 = $time_taken;
                        $input = floor($input / 1000 / 60);
                        $input2 = floor($input2 / 1000 / 60);
                        if ($input2 - $input < 210) {

                            // dd('<10');
                            //recent record taken
                            //Ignore
                            // $faceRecord->save();
                            // $this->sendSms($guardian,$faceRecord,$time_taken,'second');
                        } else {
                            //check if its the second record

                            if (sizeof(StaffFaceRecord::where('reg_no', '=', $upi_no)
                                    ->where('time_taken', '>', (string)Carbon::today()->valueOf())
                                    ->where('time_taken', '<', (string)Carbon::tomorrow()->valueOf())
                                    ->get()) == 1) {
                                // dd('second');
                                $faceRecord->status = 'exit';
                                $faceRecord->save();
                            } else {
                                // dd(sizeof(FaceRecord::where('upi_no', '=', $upi_no)
                                // ->whereDate('created_at', Carbon::today())
                                // ->get()));
                                $faceRecord->save();
                            }
                        }
                    } else {
                        //no record
                        // dd('first');
                        $faceRecord->status = 'exit';
                        $faceRecord->save();
                    }
                }
            }
        } else {

            $level = $level . "\nfaceNotCapturedCorrectly";
        }
        $record = new DeviceRecord();
        // $record->data = 'recordUpload|'.$level.implode("|",$request);
        $record->data = 'recordUpload|' . $level;
        $record->save();
    }

    public function dataPullT(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'dataPull |' . implode("|", $request->all());
        $record->save();
        $pageNumber = null;
        if ($request->has('pageNumber')) {
            $pageNumber = $request->pageNumber;
        }
        $students_count = Student::withTrashed()->where('class', '!=', '9')->get();
        // $students=Student::withTrashed()->where('class','!=','9')->paginate(100, ['*'], 'page', $pageNumber);

        $users = User::withTrashed()
            ->leftJoin('students', function ($join) {
                $join->on('users.id', '=', 'students.user_id')
                    ->where('students.class', '!=', '9');
            })
            // ->leftJoin('students', 'users.id', '=', 'students.user_id')
            ->leftJoin('staff', 'users.id', '=', 'staff.user_id')
            ->select('users.*', 'students.upi_no', 'students.first_name', 'students.surname', 'students.class', 'staff.staff_id')
            ->where('users.password', '=', null)
            ->get();
        // ->paginate(100, ['*'], 'page', $pageNumber);

        // dd($users);
        $formated_students = [];
        foreach ($users as $student) {
            if (isset($student->upi_no)) {
                // if($student->upi_no=="04308"){
                //     dd($student);
                // }
                if ($student->class != 9) {
                    array_push($formated_students, (object)[
                        'eno' => $student->upi_no, //work number
                        'idcard' => 'stream', //ID number-use as stream
                        'cardid' => 'class ' . $student->class, //card number-use as class
                        'uuid' => $student->id, //uuid
                        'name' => $student->first_name . " " . $student->surname . ' (class ' . $student->class . ')', //names
                        'type' => $student->deleted_at == NULL ? 1 : 0, //Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
                    ]);
                }
            } else if (isset($student->staff_id)) {
                array_push($formated_students, (object)[
                    'eno' => $student->staff_id, //work number
                    'idcard' => 'staff', //ID number-use as stream
                    'cardid' => $student->id, //card number-use as class
                    'uuid' => $student->id, //uuid
                    'name' => $student->name, //names
                    'type' => $student->deleted_at == NULL ? 1 : 0, //Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
                ]);
            } else {
                // array_push($formated_students, (object)[
                //     'NA'=>$student->id
                //      ]);
            }
        }
        $data = [
            'employeeList' => $formated_students,
            'count' => sizeof($formated_students), //People List page size (get the people list by page, this is the pageSize per page)
            'sum' => sizeof($formated_students), //Total number of records in the population list
        ];
        $myResponse = json_encode([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => $data
        ]);
        return response($myResponse)
            ->header('Content-Type', 'application/json');
    }

    public function dataPull(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'dataPull |' . implode("|", $request->all());
        $record->save();
        $pageNumber = null;
        if ($request->has('pageNumber')) {
            $pageNumber = $request->pageNumber;
        }
        $students_count = Student::withTrashed()->where('class', '!=', '9')->get();
        // $students=Student::withTrashed()->where('class','!=','9')->paginate(100, ['*'], 'page', $pageNumber);

        $users = User::withTrashed()
            ->leftJoin('students', function ($join) {
                $join->on('users.id', '=', 'students.user_id')
                    ->where('students.class', '!=', '9');
            })
            // ->leftJoin('students', 'users.id', '=', 'students.user_id')
            ->leftJoin('staff', 'users.id', '=', 'staff.user_id')
            ->select('users.*', 'students.upi_no', 'students.class', 'staff.staff_id')
            ->where('users.password', '=', null)
            ->get();
        // ->paginate(100, ['*'], 'page', $pageNumber);

        // dd($users);
        $formated_students = [];
        foreach ($users as $student) {
            if (isset($student->upi_no)) {
                if ($student->class != 9) {
                    array_push($formated_students, (object)[
                        'eno' => $student->upi_no, //work number
                        'idcard' => 'stream', //ID number-use as stream
                        'cardid' => 'class ' . $student->class, //card number-use as class
                        'uuid' => $student->id, //uuid
                        'name' => $student->name . ' (class ' . $student->class . ')', //names
                        'type' => $student->deleted_at == NULL ? 1 : 0, //Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
                    ]);
                }
            } else if (isset($student->staff_id)) {
                array_push($formated_students, (object)[
                    'eno' => $student->staff_id, //work number
                    'idcard' => 'staff', //ID number-use as stream
                    'cardid' => $student->id, //card number-use as class
                    'uuid' => $student->id, //uuid
                    'name' => $student->name, //names
                    'type' => $student->deleted_at == NULL ? 1 : 0, //Type 0 Delete 1 Add Update Note: Deleting a person will delete them along with their access rights configuration.
                ]);
            } else {
                // array_push($formated_students, (object)[
                //     'NA'=>$student->id
                //      ]);
            }
        }
        $data = [
            'employeeList' => $formated_students,
            'count' => sizeof($formated_students), //People List page size (get the people list by page, this is the pageSize per page)
            'sum' => sizeof($formated_students), //Total number of records in the population list
        ];
        $myResponse = json_encode([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => $data
        ]);
        return response($myResponse)
            ->header('Content-Type', 'application/json');
    }

    public function dataPullBack(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'dataPullBack|' . implode("|", $request->all());
        $record->save();
        return json_encode([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => (time() * 1000)
        ]);
    }

    public function smsCallback(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'smsCallback|' . implode("|", $request->all());
        $record->save();
        $premiumsms=new PremiumReports();
        $premiumsms->sms_id=$request->id;
        $premiumsms->status=$request->status;
        $premiumsms->phoneNumber=$request->phoneNumber;
        $premiumsms->networkCode=$request->networkCode;
        if ($request->failureReason) {
            $premiumsms->failureReason=$request->failureReason;
        }
        $premiumsms->retryCount=$request->retryCount;
        $premiumsms->save();
        return response()->json([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => (time() * 1000)
        ]);
    }
    public function subscriptionCallback(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'subscriptionCallback|' . implode("|", $request->all());
        $record->save();
        $premiumsms=new SubscriptionReports();
        if (sizeof(SubscriptionReports::where('phoneNumber','=',$request->phoneNumber)->get())>0) {
            $premiumsms=SubscriptionReports::where('phoneNumber','=',$request->phoneNumber)->get()->first();
        }
        $premiumsms->shortCode=$request->shortCode;
        $premiumsms->keyword=$request->keyword;
        $premiumsms->phoneNumber=$request->phoneNumber;
        $premiumsms->updateType=$request->updateType;
        $premiumsms->save();
        return response()->json([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => (time() * 1000)
        ]);
    }
    public function normalSmsCallback(Request $request)
    {
        $record = new DeviceRecord();
        $record->data = 'normalSmsCallback|' . implode("|", $request->all());
        $record->save();
        return json_encode([
            'code' => 200,
            'success' => true,
            'messsage' => 'successful',
            'data' => (time() * 1000)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\DeviceRecord $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function show(DeviceRecord $deviceRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\DeviceRecord $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function edit(DeviceRecord $deviceRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DeviceRecord $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeviceRecord $deviceRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\DeviceRecord $deviceRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeviceRecord $deviceRecord)
    {
        //
    }

    public function sendSms($guardian, $face_record, $time, $sms_time,$student)
    {

        $date = date("h:i a", ($time / 1000));
        $new_time = date("h:i a", strtotime('+3 hours', strtotime($date)));
        $temp = round($face_record->temperature, 1);
        if ($sms_time == 'first') {
            $templete1 = Smstemplete::where('id', '=', 1)->get()->pluck('content');

            $message1 = "Dear $guardian->fname, your child " . $face_record->student->first_name . " " . $face_record->student->surname . "  UPI:" . $face_record->student->upi_no . " has left school for home at $new_time with a temperature of $temp " . $templete1[0];
            // dd($templete);
        } else {
            $templete1 = Smstemplete::where('id', '=', 2)->get()->pluck('content');
            $message1 = "Dear $guardian->fname, your child " . $face_record->student->first_name . " " . $face_record->student->surname . " UPI:" . $face_record->student->upi_no . " has left school for home at $new_time with a temperature of $temp " . $templete1[0];
        }


        $response = Http::asForm()->withHeaders([
            'apikey' => $_ENV['SMS_NORMAL_API_KEY'],
        ])->post('https://api.africastalking.com/version1/messaging', [
            'username' => $_ENV['SMS_NORMAL_USERNAME'],
            'from' => $_ENV['SMS_FROM'],
            'message' => $message1,
            'to' => $guardian->phone,
        ]);
//        $response = Http::asForm()->withHeaders([
//            'apikey' => $_ENV['SMS_API_KEY'],
//        ])->post('https://api.africastalking.com/version1/messaging', [
//            'username' => $_ENV['SMS_USERNAME'],
//            'from' => $_ENV['SMS_FROM'],
//            'message' => $message1,
//            'to' => $guardian->phone,
//        ]);
        $sms=new SmsRecord();
        $sms->student_id=$student->id;
        $sms->recipient_id=$guardian->id;
        $sms->message=$message1;
        $sms->response_code=$response->status();
        $sms->response_text=$response->body();
        $sms->save();
        if ($response->successful()) {
            // dd($response->json()['responses'][0]['response-description']);
//            return back()->with('success', 'Message sent successfully');
        }

        // Determine if the status code is >= 400...
        if ($response->failed()) {
            // dd($response->json()['errors']['message'][0]);
//            return back()->withErrors([
//                'message' => $response->body(),
//            ]);
        }

        // Determine if the response has a 400 level status code...
        if ($response->clientError()) {

//            return back()->withErrors([
//                'message' => 'Something went wrong, could not send sms',
//            ]);
        }

        // Determine if the response has a 500 level status code...
        if ($response->serverError()) {

//            return back()->withErrors([
//                'message' => 'Something went wrong, could not send sms',
//            ]);
        }

        // $response=Http::asForm()->post('https://quicksms.advantasms.com/api/services/sendsms',[
        //     'apikey'=>$_ENV['SMS_API_KEY'],
        //     'partnerID'=>$_ENV['SMS_PATNER_ID'],
        //     'shortcode'=>$_ENV['SMS_SHORT_CODE'],
        //     'message'=>$message1,
        //     'mobile'=>$guardian->phone,
        // ]);
        // if($response->successful()){
        //     // dd($response->json()['responses'][0]['response-description']);
        //     // return back()->with('success', $response->json()['responses'][0]['response-description']);
        // }

        // // Determine if the status code is >= 400...
        // if($response->failed()){

        //     // return back()->withErrors([
        //     //     'message' => 'Something went wrong, could not send sms',
        //     // ]);
        // }

        // // Determine if the response has a 400 level status code...
        // if($response->clientError()){

        //     // return back()->withErrors([
        //     //     'message' => 'Something went wrong, could not send sms',
        //     // ]);
        // }

        // // Determine if the response has a 500 level status code...
        // if($response->serverError()){

        //     // return back()->withErrors([
        //     //     'message' => 'Something went wrong, could not send sms',
        //     // ]);
        // }

    }

    public function sendPremiumSms($guardian, $face_record, $time, $sms_time)
    {

        $date = date("h:i a", ($time / 1000));
        $new_time = date("h:i a", strtotime('+3 hours', strtotime($date)));
        $temp = round($face_record->temperature, 1);
        if ($sms_time == 'first') {
            $templete1 = Smstemplete::where('id', '=', 1)->get()->pluck('content');

            $message1 = "Dear $guardian->fname, your child " . $face_record->student->first_name . " " . $face_record->student->surname . "  UPI:" . $face_record->student->upi_no . " has arrived at school at $new_time with a temperature of $temp " . $templete1[0];
            // dd($templete);
        } else {
            $templete1 = Smstemplete::where('id', '=', 2)->get()->pluck('content');
            $message1 = "Dear $guardian->fname, your child " . $face_record->student->first_name . " " . $face_record->student->surname . " UPI:" . $face_record->student->upi_no . " has left school for home at $new_time with a temperature of $temp " . $templete1[0];
        }


        $response = Http::asForm()->withHeaders([
            'apikey' => $_ENV['SMS_API_KEY'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post('https://content.africastalking.com/version1/messaging', [
            'username' => $_ENV['SMS_USERNAME'],
            'from' => $_ENV['SMS_SHORT_CODE'],
            'message' => $message1,
            'to' => $guardian->phone,
            'keyword' => 'yes',
        ]);
        if ($response->successful()) {
//             dd('succ'.$response->body());
//            return back()->with('success', 'Message sent successfully');
        }

        // Determine if the status code is >= 400...
        if ($response->failed()) {
//             dd('failed'.$response->body());
//            return back()->withErrors([
//                'message' => $response->body(),
//            ]);
        }

        // Determine if the response has a 400 level status code...
        if ($response->clientError()) {

//            dd('clientError'.$response->body());
//            return back()->withErrors([
//                'message' => 'Something went wrong, could not send sms'.$response->body(),
//            ]);
        }

        // Determine if the response has a 500 level status code...
        if ($response->serverError()) {

//            dd('serverError'.$response->body());
//            return back()->withErrors([
//                'message' => 'Something went wrong, could not send sms'.$response->body(),
//            ]);
        }

        // $response=Http::asForm()->post('https://quicksms.advantasms.com/api/services/sendsms',[
        //     'apikey'=>$_ENV['SMS_API_KEY'],
        //     'partnerID'=>$_ENV['SMS_PATNER_ID'],
        //     'shortcode'=>$_ENV['SMS_SHORT_CODE'],
        //     'message'=>$message1,
        //     'mobile'=>$guardian->phone,
        // ]);
        // if($response->successful()){
        //     // dd($response->json()['responses'][0]['response-description']);
        //     // return back()->with('success', $response->json()['responses'][0]['response-description']);
        // }

        // // Determine if the status code is >= 400...
        // if($response->failed()){

        //     // return back()->withErrors([
        //     //     'message' => 'Something went wrong, could not send sms',
        //     // ]);
        // }

        // // Determine if the response has a 400 level status code...
        // if($response->clientError()){

        //     // return back()->withErrors([
        //     //     'message' => 'Something went wrong, could not send sms',
        //     // ]);
        // }

        // // Determine if the response has a 500 level status code...
        // if($response->serverError()){

        //     // return back()->withErrors([
        //     //     'message' => 'Something went wrong, could not send sms',
        //     // ]);
        // }

    }
}
