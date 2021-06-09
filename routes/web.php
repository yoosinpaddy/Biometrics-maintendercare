<?php

use App\Http\Controllers\DeviceRecordController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\InitializeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StudentController;
use App\Models\DeviceRecord;
use App\Models\Guardian;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [StudentController::class, 'home'])->name('school.home');
    Route::get('/parents', [StudentController::class, 'parents'])->name('school.parents');
    Route::get('/reports/{class}/{stream}', [StudentController::class, 'reports'])->name('school.reports');
    Route::get('/staff/{type}/{day}', [StudentController::class, 'staff'])->name('staff.reports');
    Route::get('/reports/{class}/{stream}/{day}', [StudentController::class, 'detailedReports'])->name('school.detailedReports');
    Route::get('/bulk_sms/{class}/{stream}', [StudentController::class, 'bulk_sms'])->name('school.bulkSms');
    Route::post('/send_bulk_sms', [StudentController::class, 'send_bulk_sms'])->name('school.send_bulk_sms');
    Route::post('/reports/poster', [StudentController::class, 'reportsPoster'])->name('school.reports.poster');
    Route::post('/staff/reports/poster/{type}', [StudentController::class, 'staffReportsPoster'])->name('staff.reports.poster');
    Route::post('/students/poster', [StudentController::class, 'studentsPoster'])->name('school.students.poster');
    Route::get('/reports/sms', [StudentController::class, 'reports_sms'])->name('school.reports.sms');
    Route::get('/streams', [StudentController::class, 'streams'])->name('school.streams');
    Route::post('/streams/new', [StudentController::class, 'streamsNew'])->name('new.streams');
    Route::post('/streams/update', [StudentController::class, 'streamsUpdate'])->name('update.streams');
    Route::get('/class/{class_name}/{stream_id}', [StudentController::class, 'myClass'])->name('school.class.data');
    Route::post('/parents/new', [StudentController::class, 'newParent'])->name('school.new.parent');
    Route::post('/staff/new', [StudentController::class, 'newStaff'])->name('school.new.staff');
    Route::post('/student/new', [StudentController::class, 'newStudent'])->name('school.new.student');
    Route::post('/student/update', [StudentController::class, 'updateStudent'])->name('school.update.student');
    Route::get('/parents', [StudentController::class, 'getParents'])->name('school.parents');
    Route::get('/parent/delete/{parent_id}', [GuardianController::class, 'delete'])->name('guardian.delete');
    Route::get('/student/delete/{student_id}', [StudentController::class, 'delete'])->name('student.delete');
    Route::post('/parent/sms/new', [GuardianController::class, 'newSms'])->name('school.send.sms');
    Route::get('/initialize', [InitializeController::class, 'initialize'])->name('school.initialize');
    Route::get('/templetes', [StudentController::class, 'templetes'])->name('sms.templete');
    Route::post('/templetesUpdate', [StudentController::class, 'templetesUpdate'])->name('sms.templetesUpdate');
    Route::post('/uploadCsv', [StudentController::class, 'uploadCsv'])->name('uploadCsv');
    Route::get('/uploadCsv', [StudentController::class, 'uploadCsv'])->name('uploadCsv');
    Route::get('/trySms', [StudentController::class, 'trySms'])->name('trySms');
});

Route::get('/', function () {
    return view('school.login');
})->name('default');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('users/{id}', function ($id) {

});


Route::get('/login', [StudentController::class, 'login'])->name('clogin');
Route::get('/register', [StudentController::class, 'register'])->name('school.register');
Route::get('/logout', [StudentController::class, 'logout'])->name('school.logout');
Route::post('/login', [StudentController::class, 'login'])->name('school.login');
Route::post('/register', [StudentController::class, 'register'])->name('school.register');
Route::post('/recoverPassword', [StudentController::class, 'recoverPassword'])->name('school.recoverPassword');
Route::post('/forgotPassword', [StudentController::class, 'forgotPassword'])->name('school.forgotPassword');
Route::get('/recoverPassword', [StudentController::class, 'recoverPassword'])->name('school.recoverPassword');
Route::get('/forgotPassword', [StudentController::class, 'forgotPassword'])->name('school.forgotPassword');




// Route::get('/device/{school_id}/posts', [PostController::class, 'index']);
// Route::get('/device/{school_id}/deviceHeartBeat', [DeviceRecordController::class, 'storeg']);
Route::post('/device/{school_id}/deviceHeartBeat', [DeviceRecordController::class, 'store']);

// Route::get('/device/{school_id}/recordUpload', [DeviceRecordController::class, 'recordUploadg']);
Route::post('/device/{school_id}/recordUpload', [DeviceRecordController::class, 'recordUpload']);

// Route::get('/device/{school_id}/dataPull', [DeviceRecordController::class, 'dataPullg']);
Route::post('/device/{school_id}/dataPull', [DeviceRecordController::class, 'dataPull']);
Route::post('/device/{school_id}/dataPullT', [DeviceRecordController::class, 'dataPullT']);

// Route::get('/device/{school_id}/dataPullBack', [DeviceRecordController::class, 'dataPullBackg']);
Route::post('/device/{school_id}/dataPullBack', [DeviceRecordController::class, 'dataPullBack']);

// Route::get('store', [PostController::class, 'store']);
