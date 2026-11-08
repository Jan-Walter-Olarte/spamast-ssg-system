<?php

namespace App\Http\Controllers;

class RegisterController extends Controller
{
    // Show the registration page.
    public function showRegistrationForm()
    {
        return view('register');
    }
}
