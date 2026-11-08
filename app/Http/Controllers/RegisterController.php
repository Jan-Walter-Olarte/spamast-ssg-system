<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Show the registration page.
    public function showRegistrationForm()
    {
        return view('register');
    }

    // Check the form, then save the new user.
    public function register(Request $request): RedirectResponse
    {
        // 1. Validate: stop here and go back with errors if anything is wrong.
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'role'        => ['required', 'in:Student,Officer,Adviser'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Find the role row that matches the chosen role name.
        $role = Role::where('name', $data['role'])->firstOrFail();

        // 3. Save the user with a hashed password.
        User::create([
            'first_name'  => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name'   => $data['last_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role_id'     => $role->id,
            'is_active'   => true,
            'is_admin'    => false,
        ]);

        // 4. Send the person to the login page with a success message.
        return redirect()->route('login')
            ->with('success', 'Registration successful. You can now log in.');
    }
}
