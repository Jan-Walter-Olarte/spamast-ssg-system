<?php

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
});

// Auth routes: only for logged-in people (any role).
Route::middleware('auth')->group(function () {
    Route::get('/main', [MainController::class, 'view'])->name('main');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile: every logged-in user can edit their own account.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
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
});

// Officer section
Route::middleware(['auth', 'role:Officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'officer'])->name('dashboard');

    // Student management (Eloquent, no "show" page)
    Route::resource('students', StudentController::class)->except(['show']);

    // Event management (the list and detail pages are shared, see below)
    Route::resource('events', OfficerEventController::class)->except(['index', 'show']);

    // Open or close a session by hand (manual events only)
    Route::patch('sessions/{session}/toggle', [SessionController::class, 'toggle'])->name('sessions.toggle');

     // Attendance: add a missing record, correct times, delete a record
    Route::post('events/{event}/attendance', [OfficerAttendanceController::class, 'store'])->name('attendance.store');
    Route::put('attendance/{attendance}', [OfficerAttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('attendance/{attendance}', [OfficerAttendanceController::class, 'destroy'])->name('attendance.destroy');
});

// Adviser section
Route::middleware(['auth', 'role:Adviser'])->prefix('adviser')->name('adviser.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adviser'])->name('dashboard');
});

// Student section
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');

    // Check in and check out of a session
    Route::post('sessions/{session}/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('sessions/{session}/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
});

// Events: everyone with an event role may view the list and the details.
Route::middleware(['auth', 'role:Officer,Adviser,Student'])->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
});

// Attendance list of one event: Officers and Advisers (the Officer controls are guarded separately).
Route::middleware(['auth', 'role:Officer,Adviser'])->group(function () {
    Route::get('/events/{event}/attendance', [EventAttendanceController::class, 'index'])->name('attendance.index');
});
