<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Update the password
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Check if user is admin to redirect to user management page
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.users.index')
                           ->with('success', 'Senha alterada com sucesso!');
        }

        return back()->with('success', 'Senha alterada com sucesso!');
    }
}
