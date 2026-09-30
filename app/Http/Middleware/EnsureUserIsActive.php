<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        // Older records and lightweight test fixtures may have a null value.
        // Only an explicit false value means the account was suspended.
        if ($request->user() && $request->user()->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'تم إيقاف هذا الحساب. تواصل مع إدارة الموقع.',
            ]);
        }

        return $next($request);
    }
}
