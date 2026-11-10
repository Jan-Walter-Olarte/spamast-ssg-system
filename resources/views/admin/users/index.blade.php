@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="page-head">
        <h1>Manage Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn">Add User</a>
    </div>

    {{-- Search and role filter (a GET form, so the address keeps the filters) --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name or email">
        <select name="role">
            <option value="">All roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>{{ $u->first_name }} {{ $u->middle_name }} {{ $u->last_name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->role->name }}</td>
                            <td>
                                @if ($u->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive / Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    {{-- Admin accounts are managed from My Profile --}}
                                    @unless ($u->hasRole('Admin'))
                                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-light btn-sm">Edit</a>

                                        <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="inline-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-light btn-sm">{{ $u->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        </form>
                                    @endunless

                                    @can('delete', $u)
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-form"
                                              onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($users->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $users->currentPage() }} of {{ $users->lastPage() }} ({{ $users->total() }} users)</span>

            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>
@endsection
