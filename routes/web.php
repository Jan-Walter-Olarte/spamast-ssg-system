<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Landing route: when someone opens the main address (/), show our landing page.
Route::get('/', function () {
    return view('landing-page');
})->name('landing');

// Register page: the route asks RegisterController to show the form.
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

// Login page: the route asks LoginController to show the form.
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
