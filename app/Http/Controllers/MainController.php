<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    // Show the main dashboard for the logged-in user.
    public function view(Request $request)
    {
        return view('main', [
            'user' => $request->user(), // the person who is logged in
        ]);
    }
}
