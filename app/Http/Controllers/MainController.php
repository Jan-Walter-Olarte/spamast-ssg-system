<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MainController extends Controller
{
    // /main now only forwards people to their own dashboard.
    public function view(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->dashboardRoute());
    }
}
