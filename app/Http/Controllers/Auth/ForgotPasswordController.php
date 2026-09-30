<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm() { return view('auth.passwords.email'); }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);
        $status = Password::sendResetLink($request->only('email'));

        if (in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER], true)) {
            return back()->with('status', __('ui.reset_link_sent'));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
