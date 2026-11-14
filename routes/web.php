<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\EventPenaltyController;
use App\Http\Controllers\Officer\PenaltyController;
use App\Http\Controllers\EventAttendanceController;
use App\Http\Controllers\Officer\AttendanceController as OfficerAttendanceController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Officer\SessionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Officer\EventController as OfficerEventController;
use App\Http\Controllers\Officer\StudentController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\InstituteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Officer\AnnouncementController as OfficerAnnouncementController;
use App\Http\Controllers\Student\MyRecordsController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Officer\StudentImportController;

// Landing route: when someone opens the main address (/), show our landing page.
Route::get('/', function () {
    return view('landing-page');
})->name('landing');

// Guest routes: only for people who are NOT logged in.
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');

    // Forgot password: ask for a link, then choose a new password.
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// Auth routes: only for logged-in people (any role).
Route::middleware('auth')->group(function () {
    Route::get('/main', [MainController::class, 'view'])->name('main');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile: every logged-in user can edit their own account.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Set a new password (forced for accounts with a temporary password)
    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('/password/change', [PasswordController::class, 'update'])->name('password.change.update');


    // Notifications: every logged-in user has their own
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

// Admin section
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // User management (no "show" page)
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    // Institutes (Query Builder)
    Route::resource('institutes', InstituteController::class)->except(['show']);

    // Programs (Query Builder)
    Route::resource('programs', ProgramController::class)->except(['show']);

    // The audit log (read-only)
    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
});

// Officer section
Route::middleware(['auth', 'role:Officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'officer'])->name('dashboard');

    // Bulk import of students from a CSV file (above the resource, so "import" is never read as a student)
    Route::get('students/import', [StudentImportController::class, 'create'])->name('students.import');
    Route::post('students/import', [StudentImportController::class, 'store'])->name('students.import.store');

    // Student management (Eloquent, no "show" page)
    Route::resource('students', StudentController::class)->except(['show']);

    // Event management (the list and detail pages are shared, see below)
    Route::resource('events', OfficerEventController::class)->except(['index', 'show']);

    // Open or close a session by hand (manual events only)
    Route::patch('sessions/{session}/toggle', [SessionController::class, 'toggle'])->name('sessions.toggle');

    // Generate a new check-in code for a session
    Route::patch('sessions/{session}/code', [SessionController::class, 'regenerateCode'])->name('sessions.code');

    // Attendance: add a missing record, correct times, delete a record
    Route::post('events/{event}/attendance', [OfficerAttendanceController::class, 'store'])->name('attendance.store');
    Route::put('attendance/{attendance}', [OfficerAttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('attendance/{attendance}', [OfficerAttendanceController::class, 'destroy'])->name('attendance.destroy');

    // Fines and sanctions
    Route::post('events/{event}/penalties/generate', [PenaltyController::class, 'generate'])->name('penalties.generate');
    Route::put('penalties/{penalty}', [PenaltyController::class, 'update'])->name('penalties.update');
    Route::delete('penalties/{penalty}', [PenaltyController::class, 'destroy'])->name('penalties.destroy');

    // Announcements: post, edit and delete (reading is shared, see the bottom of the file)
    Route::resource('announcements', OfficerAnnouncementController::class)->except(['index', 'show']);
});

// Adviser section
Route::middleware(['auth', 'role:Adviser'])->prefix('adviser')->name('adviser.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adviser'])->name('dashboard');
});

// Student section
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');

    // The student's own records
    Route::get('my-attendance', [MyRecordsController::class, 'attendance'])->name('my-attendance');
    Route::get('my-fines', [MyRecordsController::class, 'fines'])->name('my-fines');

    // Check in and check out of a session
    // At most 10 tries per minute, so codes cannot be guessed by brute force.
    Route::post('sessions/{session}/check-in', [AttendanceController::class, 'checkIn'])->middleware('throttle:10,1')->name('attendance.check-in');
    Route::post('sessions/{session}/check-out', [AttendanceController::class, 'checkOut'])->middleware('throttle:10,1')->name('attendance.check-out');
});

// Events: everyone with an event role may view the list and the details.
Route::middleware(['auth', 'role:Officer,Adviser,Student'])->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    // The month calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
});

// Attendance list of one event: Officers and Advisers (the Officer controls are guarded separately).
Route::middleware(['auth', 'role:Officer,Adviser'])->group(function () {
    Route::get('/events/{event}/attendance', [EventAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/events/{event}/penalties', [EventPenaltyController::class, 'index'])->name('penalties.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{event}', [ReportController::class, 'show'])->name('reports.show');
});

// Announcements: everyone with a role may read them.
Route::middleware(['auth', 'role:Officer,Adviser,Student'])->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
});
