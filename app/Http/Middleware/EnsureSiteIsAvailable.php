<?php

namespace App\Http\Middleware;

use App\SiteSetting;
use Closure;
use Illuminate\Http\Request;

class EnsureSiteIsAvailable
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = $request->is('login', 'logout', 'admin', 'admin/*') || (bool) $request->user()?->is_admin;

        if (SiteSetting::getValue('maintenance_mode', false) && !$allowed) {
            return response()->view('maintenance', ['siteSettings' => SiteSetting::allWithDefaults()], 503, ['Retry-After' => 3600]);
        }

        return $next($request);
    }
}
