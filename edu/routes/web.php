<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PensionController;
use App\Http\Controllers\TeacherResignationController;
use App\Http\Controllers\HRResignationController;
use App\Http\Controllers\PrincipalResignationController;
use App\Http\Controllers\HRPrincipalResignationController;
use App\Http\Controllers\StaffResignationController;
use App\Http\Controllers\HRStaffResignationController;
use App\Http\Controllers\teacher\TeacherDashboardController;
use App\Http\Controllers\principal\PrincipalDashboardController;
use App\Http\Controllers\staff\StaffDashboardController;

/*
|--------------------------------------------------------------------------
| ROOT ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/

Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change.password');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public Routes for Applicants
|--------------------------------------------------------------------------
*/
Route::get('/accept-offer/{application_id}', [PlacementController::class, 'acceptOffer'])->name('offer.accept');
Route::get('/reject-offer/{application_id}', [PlacementController::class, 'rejectOffer'])->name('offer.reject');

/*
|--------------------------------------------------------------------------
| STAFF MODULE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.staff'])->group(function () {
    Route::get('/staff-dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    
    Route::get('/staff/resignation/create', [StaffResignationController::class, 'create'])->name('staff.resignations.create');
    Route::post('/staff/resignation/store', [StaffResignationController::class, 'store'])->name('staff.resignations.store');
    Route::get('/staff/resignation/status', [StaffResignationController::class, 'index'])->name('staff.resignations.index');
    Route::post('/staff/resignation/cancel', [StaffResignationController::class, 'cancel'])->name('staff.resignations.cancel');
});

/*
|--------------------------------------------------------------------------
| HR MODULE
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth.hr'])->group(function () {
    // ================================================================
    // HOME & DASHBOARD
    // ================================================================
    Route::get('/hr/home', [HomeController::class, 'index'])->name('hr.home');
    Route::get('/hr/dashboard', [DashboardController::class, 'index'])->name('hr.dashboard');
    
    // ================================================================
    // CALENDAR
    // ================================================================
    Route::get('/takwim', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar-events', [CalendarController::class, 'getEvents']);

    // ================================================================
    // PENSION ROUTES
    // ================================================================
    Route::prefix('pension')->group(function () {
        Route::get('/dashboard', [PensionController::class, 'dashboard'])->name('pension.dashboard');
        Route::post('/teacher/{id}/set-pension', [PensionController::class, 'setTeacherPension'])->name('pension.set-teacher');
        Route::post('/staff/{id}/set-pension', [PensionController::class, 'setStaffPension'])->name('pension.set-staff');
        Route::post('/principal/{id}/set-pension', [PensionController::class, 'setPrincipalPension'])->name('pension.set-principal');
        Route::delete('/{type}/{id}/remove-pension', [PensionController::class, 'removePension'])->name('pension.remove');
        Route::get('/resigned-teachers', [PensionController::class, 'resignedTeachers'])->name('pension.resigned-teachers');
        Route::get('/resigned-staff', [PensionController::class, 'resignedStaff'])->name('pension.resigned-staff');
        Route::get('/resigned-principals', [PensionController::class, 'resignedPrincipals'])->name('pension.resigned-principals');
        Route::get('/report', [PensionController::class, 'report'])->name('pension.report');
        Route::get('/export', [PensionController::class, 'export'])->name('pension.export');
    });

    // ================================================================
    // TEACHER RESIGNATION ROUTES (HR)
    // ================================================================
    Route::prefix('hr')->group(function () {
        Route::get('/resignations/pending', [HRResignationController::class, 'pending'])->name('hr.resignations.pending');
        Route::get('/resignations/all', [HRResignationController::class, 'all'])->name('hr.resignations.all');
        Route::get('/resignations/{id}', [HRResignationController::class, 'show'])->name('hr.resignations.show');
        Route::post('/resignations/{id}/approve', [HRResignationController::class, 'approve'])->name('hr.resignations.approve');
        Route::post('/resignations/{id}/reject', [HRResignationController::class, 'reject'])->name('hr.resignations.reject');
    });

    // ================================================================
    // PRINCIPAL RESIGNATION ROUTES (HR)
    // ================================================================
    Route::prefix('hr')->group(function () {
        Route::get('/principal-resignations/pending', [HRPrincipalResignationController::class, 'pending'])->name('hr.principal-resignations.pending');
        Route::get('/principal-resignations/all', [HRPrincipalResignationController::class, 'all'])->name('hr.principal-resignations.all');
        Route::get('/principal-resignations/{id}', [HRPrincipalResignationController::class, 'show'])->name('hr.principal-resignations.show');
        Route::post('/principal-resignations/{id}/approve', [HRPrincipalResignationController::class, 'approve'])->name('hr.principal-resignations.approve');
        Route::post('/principal-resignations/{id}/reject', [HRPrincipalResignationController::class, 'reject'])->name('hr.principal-resignations.reject');
    });

    // ================================================================
    // STAFF RESIGNATION ROUTES (HR)
    // ================================================================
    Route::prefix('hr')->group(function () {
        Route::get('/staff-resignations/pending', [HRStaffResignationController::class, 'pending'])->name('hr.staff-resignations.pending');
        Route::get('/staff-resignations/all', [HRStaffResignationController::class, 'all'])->name('hr.staff-resignations.all');
        Route::get('/staff-resignations/{id}', [HRStaffResignationController::class, 'show'])->name('hr.staff-resignations.show');
        Route::post('/staff-resignations/{id}/approve', [HRStaffResignationController::class, 'approve'])->name('hr.staff-resignations.approve');
        Route::post('/staff-resignations/{id}/reject', [HRStaffResignationController::class, 'reject'])->name('hr.staff-resignations.reject');
    });

    // ================================================================
    // SCHOOL MANAGEMENT
    // ================================================================
    Route::get('/school-list', [SchoolController::class, 'list'])->name('school.list');
    Route::get('/school-records', [SchoolController::class, 'records'])->name('school.records');
    Route::get('/school/organizations', [SchoolController::class, 'organizations'])->name('school.organizations');
    Route::get('/school/upload', [SchoolController::class, 'uploadForm'])->name('school.upload');
    Route::post('/school/upload', [SchoolController::class, 'importCSV'])->name('school.import');
    Route::get('/school/download-template', [SchoolController::class, 'downloadTemplate'])->name('school.download-template');
    Route::post('/school/store', [SchoolController::class, 'store'])->name('school.store');
    Route::get('/school/{id}', [SchoolController::class, 'show'])->name('school.show');
    Route::get('/school/{id}/edit', [SchoolController::class, 'edit'])->name('school.edit');
    Route::put('/school/{id}', [SchoolController::class, 'update'])->name('school.update');
    Route::delete('/school/{id}', [SchoolController::class, 'destroy'])->name('school.destroy');

    // ================================================================
    // PRINCIPAL MANAGEMENT
    // ================================================================
    Route::get('/principals', [PrincipalController::class, 'index'])->name('principal.index');
    Route::get('/principals/stopped', [PrincipalController::class, 'stopped'])->name('principal.stopped');
    Route::get('/principals/template', [PrincipalController::class, 'template'])->name('principals.template');
    Route::post('/principals/import', [PrincipalController::class, 'import'])->name('principals.import');
    Route::post('/principals', [PrincipalController::class, 'store'])->name('principals.store');
    Route::get('/principals/{principalID}', [PrincipalController::class, 'show'])->name('principals.show');
    Route::get('/principals/{principalID}/edit', [PrincipalController::class, 'edit'])->name('principals.edit');
    Route::put('/principals/{principalID}', [PrincipalController::class, 'update'])->name('principals.update');
    Route::post('/principals/{principalID}/terminate', [PrincipalController::class, 'terminate'])->name('principals.terminate');

    // ================================================================
    // STAFF MANAGEMENT
    // ================================================================
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.list');
    Route::get('/staff/template', [StaffController::class, 'template'])->name('staff.template');
    Route::get('/staff/stopped', [StaffController::class, 'stopped'])->name('staff.stopped');
    Route::get('/staff/resigned', [StaffController::class, 'resigned'])->name('staff.resigned');
    Route::post('/staff/import', [StaffController::class, 'import'])->name('staff.import');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staffID}', [StaffController::class, 'show'])->name('staff.show');
    Route::get('/staff/{staffID}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staffID}', [StaffController::class, 'update'])->name('staff.update');
    Route::post('/staff/{staffID}/terminate', [StaffController::class, 'terminate'])->name('staff.terminate');

    // ================================================================
    // TEACHER MANAGEMENT
    // ================================================================
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teacher-details', [TeacherController::class, 'details'])->name('teachers.details');
    Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::get('/teachers/template', [TeacherController::class, 'template'])->name('teachers.template');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/stopped', [TeacherController::class, 'stopped'])->name('teachers.stopped');
    Route::get('/teachers/{teacherID}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{teacherID}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacherID}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::post('/teachers/{teacherID}/terminate', [TeacherController::class, 'terminate'])->name('teachers.terminate');

    // ================================================================
    // PLACEMENT MODULE
    // ================================================================
    
    // Offer Letters
    Route::get('/offer/letters', [PlacementController::class, 'offerLetters'])->name('offer.index');
    Route::get('/offer-letters', [PlacementController::class, 'offerLetters'])->name('offer.letters');
    Route::get('/offer-preview/{application_id}', [PlacementController::class, 'previewOfferLetter'])->name('offer.preview');
    Route::post('/offer/send', [PlacementController::class, 'sendOfferLetter'])->name('offer.send');
    Route::get('/offer/tracking', [PlacementController::class, 'offerTracking'])->name('offer.tracking');
    Route::get('/sent-offers', [PlacementController::class, 'viewSentOffers'])->name('offer.sent');
    
    // School Assignment for Offers
    Route::post('/placement/assign-school', [PlacementController::class, 'assignSchool'])->name('offer.assign-school');
    Route::post('/placement/remove-school', [PlacementController::class, 'removeSchool'])->name('offer.remove-school');
    
    // Confirmation Letters
    Route::get('/confirmation/letters', [PlacementController::class, 'confirmationLetters'])->name('confirmation.index');
    Route::get('/confirmation-letters', [PlacementController::class, 'confirmationLetters'])->name('confirmation.letters');
    Route::get('/confirmation-preview/{application_id}', [PlacementController::class, 'previewConfirmationLetter'])->name('confirmation.preview');
    Route::post('/confirmation/send', [PlacementController::class, 'sendConfirmationLetter'])->name('confirmation.send');
    Route::get('/confirmation/tracking', [PlacementController::class, 'confirmationTracking'])->name('confirmation.tracking');
    Route::post('/confirmation/convert/{gn_id}', [PlacementController::class, 'convertToTeacher'])->name('confirmation.convert');
    
    // Placement (Guru New Assignment)
    Route::get('/placement', [PlacementController::class, 'placement'])->name('placement.index');
    Route::post('/placement/assign', [PlacementController::class, 'assignToSchool'])->name('placement.assign');
    Route::get('/placement/records', [PlacementController::class, 'placementRecords'])->name('placement.records');
});

// ================================================================
// ORGANIZATION MANAGEMENT - MOVED OUTSIDE HR GROUP FOR TESTING
// ================================================================
Route::get('/orgs', [OrganizationController::class, 'index'])->name('orgs.index');
Route::get('/orgs/download-template', [OrganizationController::class, 'downloadTemplate'])->name('orgs.download-template');
Route::post('/orgs', [OrganizationController::class, 'store'])->name('orgs.store');
    Route::get('/orgs/create', [OrganizationController::class, 'create'])->name('orgs.create');
    Route::get('/orgs/{id}', [OrganizationController::class, 'show'])->name('orgs.show');
    Route::get('/orgs/{id}/edit', [OrganizationController::class, 'edit'])->name('orgs.edit');
    Route::put('/orgs/{id}', [OrganizationController::class, 'update'])->name('orgs.update');
    Route::delete('/orgs/{id}', [OrganizationController::class, 'destroy'])->name('orgs.destroy');
    Route::post('/orgs/import', [OrganizationController::class, 'import'])->name('orgs.import');



/*
|--------------------------------------------------------------------------
| TEACHER MODULE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.teacher'])->group(function () {
    Route::get('/teacher-dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/resignation/create', [TeacherResignationController::class, 'create'])->name('teacher.resignations.create');
    Route::post('/teacher/resignation/store', [TeacherResignationController::class, 'store'])->name('teacher.resignations.store');
    Route::get('/teacher/resignations', [TeacherResignationController::class, 'index'])->name('teacher.resignations.index');
    Route::post('/teacher/resignation/cancel', [TeacherResignationController::class, 'cancel'])->name('teacher.resignations.cancel');
});

/*
|--------------------------------------------------------------------------
| PRINCIPAL MODULE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.principal'])->group(function () {
    Route::get('/dashboard', [PrincipalDashboardController::class, 'index'])->name('principal.dashboard');
    Route::get('/principal/teachers', [PrincipalController::class, 'teacherList'])->name('principal.teachers.list');
    Route::get('/principal/teachers/export', [PrincipalController::class, 'exportTeachers'])->name('principal.teachers.export');
    Route::get('/principal/teachers/stats', [PrincipalController::class, 'getTeacherStats'])->name('principal.teachers.stats');
    Route::get('/principal/teachers/{teacherID}', [PrincipalController::class, 'showTeacher'])->name('principal.teachers.show');
    Route::get('/principal/calendar', [PrincipalController::class, 'calendar'])->name('principal.calendar');
    Route::get('/principal/resigned', [PrincipalController::class, 'resignList'])->name('principal.resigned');
    
    Route::get('/principal/resignation/create', [PrincipalResignationController::class, 'create'])->name('principal.resignations.create');
    Route::post('/principal/resignation/store', [PrincipalResignationController::class, 'store'])->name('principal.resignations.store');
    Route::get('/principal/resignations', [PrincipalResignationController::class, 'index'])->name('principal.resignations.index');
    Route::post('/principal/resignation/cancel', [PrincipalResignationController::class, 'cancel'])->name('principal.resignations.cancel');
});

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/debug-staff-session', function () {
    return [
        'session_has_user' => session()->has('user'),
        'session_role' => session('role'),
        'session_user_id' => session('userID'),
        'session_user_name' => session('userName'),
        'session_all' => session()->all(),
        'user_object' => session('user') ? [
            'class' => get_class(session('user')),
            'staffID' => session('user')->staffID ?? null,
            'email' => session('user')->email ?? null,
            'role' => session('user')->role ?? null,
        ] : null,
        'session_driver' => config('session.driver'),
        'session_lifetime' => config('session.lifetime'),
        'session_id' => session()->getId(),
    ];
});