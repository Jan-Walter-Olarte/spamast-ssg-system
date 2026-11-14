<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Show the settings form.
    public function edit()
    {
        return view('admin.settings.edit', [
            'schoolName'  => Setting::schoolName(),
            'defaultFine' => Setting::defaultFine(),
        ]);
    }

    // Validate and save the settings.
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_name'  => ['required', 'string', 'max:120'],
            'default_fine' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        // Remember the old values for the audit log.
        $oldName = Setting::schoolName();
        $oldFine = Setting::defaultFine();

        $newFine = number_format((float) $data['default_fine'], 2, '.', '');

        Setting::put('school_name', $data['school_name']);
        Setting::put('default_fine', $newFine);

        AuditLog::record(
            'settings.updated',
            "Changed the settings. School name: \"{$oldName}\" to \"{$data['school_name']}\"; "
                . "default fine: {$oldFine} to {$newFine}."
        );

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Settings saved.');
    }
}
