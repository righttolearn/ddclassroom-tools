<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\ClassResponsibleController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HandBookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\ShoqaController;
use App\Http\Controllers\StudentClassPromotionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentResultCardController;
use App\Http\Controllers\StudentResultController;
use App\Http\Controllers\StudentRetakeOpportunityController;
use App\Http\Controllers\SubGradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\MonthlyAttendanceLogController;
use App\Http\Controllers\SkippedGradeSubjectController;
use App\Http\Controllers\TwoFactorAuthSetupController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::post('general-attendance-score', [AttendanceLogController::class, 'generalAttendanceScore'])->name('general-attendance-score');
    Route::get('create-student-shoqa-score', [ShoqaController::class, 'createStudentShoqaScore']);
    Route::get('clear-all-attendance-log', [AttendanceLogController::class, 'clearAllAttendanceLog']);
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);

    // Report routes
    Route::controller(ReportController::class)->group(function () {
        Route::get('reports/student-results', 'studentResults')->name('reports.student-results');
        Route::get('reports/subject-scores', 'subjectScores')->name('reports.subject-scores');
        Route::get('reports/subject-statistics', 'subjectStatistics')->name('reports.subject-statistics');
        Route::get('reports/grade-9', 'grade9Report')->name('reports.grade-9');
        Route::get('reports/all-students-subject-scores', 'allStudentsSubjectScores')->name('reports.all-students-subject-scores');

        // Export routes
        Route::get('reports/student-results/export', 'exportStudentResults')->name('reports.student-results.export');
        Route::get('reports/subject-scores/export', 'exportSubjectScores')->name('reports.subject-scores.export');
        Route::get('reports/subject-statistics/export', 'exportSubjectStatistics')->name('reports.subject-statistics.export');
        Route::get('reports/grade-9/export', 'exportGrade9')->name('reports.grade-9.export');
        Route::get('reports/all-students-subject-scores/export', 'exportAllStudentsSubjectScores')->name('reports.all-students-subject-scores.export');
    });

    Route::get('students/create/multiple', [StudentController::class, 'createMultipleStudents']);
    Route::get('students/retake-opportunities', [StudentRetakeOpportunityController::class, 'index'])->name('students.retake-opportunities');
    Route::get('students/retake-opportunities/export', [StudentRetakeOpportunityController::class, 'export'])->name('students.retake-opportunities.export');
    Route::resource('students', StudentController::class);
    Route::get('edit-student-info', [StudentController::class, 'editStudentInfo']);

    Route::post('update-student-info', [StudentController::class, 'updateStudentInfo'])->name('students.update-student-info');

    Route::post('storeMultipleStudents', [StudentController::class, 'storeMultipleStudents'])->name('students.store-multiple-student');
    Route::post('student-uploading-sample-file', [StudentController::class, 'studentUpladingSampleFile'])->name('student-uploading-sample-file');
    Route::get('email-handbook/{uuid}', [StudentController::class, 'emailHandbook']);
    Route::get('handbooks', [HandBookController::class, 'index'])->name('handbooks.index');
    Route::post('handbooks.send-handbook', [HandBookController::class, 'sendHandbook'])->name('handbooks.send-handbook');

    Route::controller(StudentResultController::class)->group(function () {
        Route::get('student-result', 'index')->name('student-result.index');
        Route::post('student-result.send-score', 'sendScore')->name('student-result.send-score');
        Route::get('student-result/create/multiple', 'createMultipleStudentScores');
        Route::post('storeMultipleStudentsScore', 'storeMultipleStudentsScore')->name('student-result.store-multiple-student-score');
    });

    Route::controller(AttendanceController::class)->group(function () {
        Route::get('student-attendance', 'index')->name('student-attendance.index');
        Route::post('student-attendance.send-score', 'sendScore')->name('student-attendance.send-attendance');
        Route::get('student-attendance/create/multiple', 'createMultipleStudentAttendance');
        Route::post('store-multiple-students-attendance', 'storeMultipleStudentsAttendance')->name('student-attendance.store-multiple-student-attendance');
    });

    Route::controller(AttendanceLogController::class)->group(function () {
        Route::get('student-attendance-log', 'index')->name('student-attendance-log.index');
        Route::post('student-attendance-log.send-score', 'sendScore')->name('student-attendance-log.send-attendance');
        Route::get('student-attendance-log/create/multiple', 'createMultipleStudentAttendance');
        Route::post('store-multiple-students-attendance-log', 'storeMultipleStudentsAttendanceLog')->name('student-attendance-log.store-multiple-student-attendance');
        Route::get('reports/attendance-log', 'studentAttendanceLogReports')->name('reports.attendance-log');
        Route::get('get-attendance-log-report-as-excel', 'getAttendanceLogReportAsExcel');
    });

    Route::resource('subjects', SubjectController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('years', YearController::class);
    Route::post('toggle-country-is-active', [CountryController::class, 'toggleIsActive']);
    Route::resource('results', ResultController::class);
    Route::post('toggle-result-is-active', [ResultController::class, 'toggleIsActive']);
    Route::resource('sub-grades', SubGradeController::class);
    Route::resource('sub-grade-subject-semesters', \App\Http\Controllers\SubGradeSubjectSemesterController::class);
    Route::resource('class-responsible', ClassResponsibleController::class);
    Route::post('toggle-sub-grade-is-active', [SubGradeController::class, 'toggleIsActive']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('logs', [LogViewerController::class, 'index']);

    Route::controller(StudentResultCardController::class)->group(function () {
        Route::get('student-hand-book/{uuid}', 'studentHandBook');
        Route::get('student-result-card/{uuid}/{year}/{studentResult}', 'studentResultCard');
    });

    Route::controller(ScoreController::class)->group(function () {
        Route::get('student-score', 'index')->name('student-score.index');
        Route::get('student-score/create', 'create')->name('student-score.create');
        Route::get('student-score/create-scores', 'createScores')->name('student-score.create-scores');
        Route::get('student-score/delete-scores', 'deleteScores')->name('student-score.delete-scores');
        Route::get('student-score/{score}/edit', 'edit');
        Route::post('student-score', 'store')->name('student-score.store');
        Route::post('delete-scores', 'deleteStudentScores')->name('delete-scores');
        Route::put('student-score/{score}', 'update')->name('student-score.update');
        Route::get('add-midterm-score-based-on-final', 'createMidtermScoreBasedOnFinal');
        Route::post('add-midterm-score-based-on-final', 'storeMidtermScoreBasedOnFinal')->name('add-midterm-score-based-on-final');
    });

    Route::post('get-students', [EnrollmentController::class, 'getStudents'])->name('get-students');

    Route::controller(ShoqaController::class)->group(function () {
        Route::get('shoqa', 'index');
        Route::get('get-shoqa-as-excel', 'getShoqaAsExcel');
    });

    Route::get('get-student-result-as-excel', [StudentResultController::class, 'getStudentResultAsExcel']);

    Route::controller(StudentClassPromotionController::class)->group(function () {
        Route::get('student-class-promotion', 'index');
        Route::post('student-class-promotion', 'store')->name('student-class-promotion');
    });

    Route::resource('skipped-grade-subjects',SkippedGradeSubjectController::class);

    // Backup routes - only accessible by user id = 1 (checked in controller)
    Route::controller(BackupController::class)->group(function () {
        Route::get('backup', 'index')->name('backup.index');
        Route::get('backup/download/{disk}/{path}', 'download')->name('backup.download')->where('path', '.*');
    });

    Route::prefix('monthly-attendance-logs')->controller(MonthlyAttendanceLogController::class)->group(function(){
        Route::get('/','index')->name('monthly-attendance-logs.index');
        Route::get('/export', 'export')->name('monthly-attendance-logs.export');
        Route::get('/generate', 'create')->name('monthly-attendance-logs.create');
        Route::post('/generate', 'generate')->name('monthly-attendance-logs.generate');
        Route::post('/send-emails','sendEmails')->name('monthly-attendance-logs.send-emails');
    });
});

Route::middleware(['auth'])
    ->controller(TwoFactorAuthSetupController::class)
    ->group(function () {
        Route::get('2fa/index', 'index');
        Route::get('2fa/enable', 'store')->name('enable-2fa');
        Route::post('2fa/disable', 'destroy')->name('disable-2fa');
    });
Route::get('two-factor-challenge-backup-code', [TwoFactorChallengeController::class, 'index']);
Route::get('result-card/{uuid}/{year}/{studentResult}', [StudentResultCardController::class, 'resultCard']);
Route::get('certificate/{uuid}/{year}/{studentResult}/{semester?}', [StudentResultCardController::class, 'grade9Certificate']);
