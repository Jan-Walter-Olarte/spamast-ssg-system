<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    // Save the logged-in user's chosen theme. Called in the background when the picker changes.
    public function update(Request $request): Response
    {
        $data = $request->validate([
            // Only the six known theme names are accepted.
            'theme' => ['required', Rule::in(User::THEMES)],
        ]);

        // The user comes from the session, never from the request.
        $request->user()->update(['theme' => $data['theme']]);

        return response()->noContent();
    }
}
