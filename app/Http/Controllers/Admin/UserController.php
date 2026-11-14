<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // The roles an Admin may assign on this screen.
    private const MANAGED_ROLES = ['Officer', 'Adviser', 'Student'];

    // List users with search, role filter and pagination.
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $roleId = $request->query('role');

        $users = User::with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleId, fn ($query) => $query->where('role_id', $roleId))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users'  => $users,
            'roles'  => Role::orderBy('name')->get(),
            'search' => $search,
            'roleId' => $roleId,
        ]);
    }

    // Show the empty create form.
    public function create()
    {
        return view('admin.users.create', [
            'user'  => new User(),
            'roles' => $this->managedRoles(),
        ]);
    }

    // Validate and save a new user.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'role_id'     => ['required', Rule::exists('roles', 'id')->whereIn('name', self::MANAGED_ROLES)],
            'is_active'   => ['required', 'boolean'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name'  => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name'   => $data['last_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role_id'     => $data['role_id'],
            'is_active'   => $request->boolean('is_active'),
            'is_admin'    => false,
            // The Admin chose this password, so the user must replace it at first login.
            'must_change_password' => true,
        ]);

        DashboardCache::people();

        AuditLog::record(
            'user.created',
            "Created the account of {$user->full_name} ({$user->email}) as " . $user->role->name . '.',
            $user
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    // Show the edit form.
    public function edit(User $user)
    {
        $this->ensureManageable($user);

        return view('admin.users.edit', [
            'user'  => $user,
            'roles' => $this->managedRoles(),
        ]);
    }

    // Validate and save changes.
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id'     => ['required', Rule::exists('roles', 'id')->whereIn('name', self::MANAGED_ROLES)],
            'is_active'   => ['required', 'boolean'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldRole = $user->role->name;

        $user->first_name  = $data['first_name'];
        $user->middle_name = $data['middle_name'] ?? null;
        $user->last_name   = $data['last_name'];
        $user->email       = $data['email'];
        $user->role_id     = $data['role_id'];
        $user->is_active   = $request->boolean('is_active');

        $passwordReset = ! empty($data['password']);

        if ($passwordReset) {
            $user->password = Hash::make($data['password']);
            // A password reset by an Admin must be replaced by the user at next login.
            $user->must_change_password = true;
        }

        $user->save();

        DashboardCache::people();

        $newRole = Role::find($data['role_id'])->name;

        AuditLog::record(
            'user.updated',
            "Updated the account of {$user->full_name} ({$user->email})."
                . ($oldRole === $newRole ? '' : " Role changed from {$oldRole} to {$newRole}.")
                . ($passwordReset ? ' Password was reset.' : ''),
            $user
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    // Activate or deactivate an account (this is how sign-ups are approved).
    public function toggle(User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        $user->update(['is_active' => ! $user->is_active]);

        DashboardCache::people();

        // Tell the user when their account is approved. They will see it at their first login.
        if ($user->is_active) {
            $user->notify(new SystemNotification(
                'Account approved',
                'Your account is now active. Welcome to the SPAMAST SSG system!',
                route($user->dashboardRoute(), [], false)
            ));
        }

        AuditLog::record(
            $user->is_active ? 'user.activated' : 'user.deactivated',
            ($user->is_active ? 'Activated' : 'Deactivated') . " the account of {$user->full_name} ({$user->email}).",
            $user
        );

        return back()->with(
            'success',
            $user->is_active ? 'Account activated.' : 'Account deactivated.'
        );
    }

    // Delete a user, but only if the policy allows it.
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        // Write the description first, because the account is about to disappear.
        $text = "Deleted the account of {$user->full_name} ({$user->email}).";

        $user->delete();

        DashboardCache::people();

        AuditLog::record('user.deleted', $text, $user);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted.');
    }

    // The three roles shown in the dropdown.
    private function managedRoles()
    {
        return Role::whereIn('name', self::MANAGED_ROLES)->orderBy('name')->get();
    }

    // Admin accounts are managed from My Profile, not from this screen.
    private function ensureManageable(User $user): void
    {
        abort_if($user->hasRole('Admin'), 403, 'Admin accounts are managed from My Profile.');
    }
}
