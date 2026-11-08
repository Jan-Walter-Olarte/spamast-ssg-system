<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    // Show the login page.
    public function showLoginForm()
    {
        return view('login');
    }
}
