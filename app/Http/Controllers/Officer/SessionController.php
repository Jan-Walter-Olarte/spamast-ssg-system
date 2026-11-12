<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\EventSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
    // Open or close check-in or check-out of one session (manual events only).
    public function toggle(Request $request, EventSession $session): RedirectResponse
    {
        $session->load('event');

        // Only Officers may change an event (EventPolicy).
        Gate::authorize('update', $session->event);

        // Scheduled events open and close by the clock, so they cannot be toggled.
        abort_unless($session->event->availability === 'manual', 403, 'Only manual events can be opened or closed by hand.');

        $data = $request->validate([
            'type' => ['required', 'in:check_in,check_out'],
        ]);

        $column = $data['type'] . '_open';

        $session->update([$column => ! $session->$column]);

        $label = $data['type'] === 'check_in' ? 'Check-in' : 'Check-out';

        return back()->with('success', $label . ($session->$column ? ' opened.' : ' closed.'));
    }
}
