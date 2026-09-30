<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireVerifiedEmail
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedEmail() && !$request->routeIs(
            'verification.notice',
            'verification.verify',
            'verification.send',
            'logout'
        )) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
