<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // All four share the same temporary view for now.
    // In Stage B2 each one gets its own real data.

    public function admin(Request $request)
    {
        return view('main', ['user' => $request->user()]);
    }

    public function officer(Request $request)
    {
        return view('main', ['user' => $request->user()]);
    }

    public function adviser(Request $request)
    {
        return view('main', ['user' => $request->user()]);
    }

    public function student(Request $request)
    {
        return view('main', ['user' => $request->user()]);
    }
}
