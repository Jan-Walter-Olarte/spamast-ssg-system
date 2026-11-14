<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Read-only list of the audit log, newest first. There is no edit and no delete.
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $logs = AuditLog::with('user')
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->query('action')))
            ->when($request->filled('search'), fn ($query) => $query->where('description', 'like', "%{$search}%"))
            ->latest('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-log.index', [
            'logs'    => $logs,
            'actions' => AuditLog::select('action')->distinct()->orderBy('action')->pluck('action'),
            'search'  => $search,
            'action'  => $request->query('action'),
        ]);
    }
}
