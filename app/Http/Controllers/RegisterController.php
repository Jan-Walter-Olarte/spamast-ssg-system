<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\DashboardCache;

class RegisterController extends Controller
{
    // Show the registration page.
    public function showRegistrationForm()
    {
        return view('register');
    }

    // Check the form, then save the new user as an inactive Student.
    public function register(Request $request): RedirectResponse
    {
        // 1. Validate. There is no role field any more.
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Every public sign-up is a Student. The role is set here, in code.
        $studentRole = Role::where('name', 'Student')->firstOrFail();

        // 3. Save the user, inactive until an Admin approves the account.
        User::create([
            'first_name'  => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name'   => $data['last_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role_id'     => $studentRole->id,
            'is_active'   => false,
            'is_admin'    => false,
        ]);

        DashboardCache::admin();

        // 4. Back to the login page with a message.
        return redirect()->route('login')
            ->with('success', 'Registration successful. Your account is awaiting Admin approval.');
    }
}
