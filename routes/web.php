<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\LoginController;
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

// Auth routes: only for logged-in people.
Route::middleware('auth')->group(function () {
    Route::get('/main', [MainController::class, 'view'])->name('main');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
