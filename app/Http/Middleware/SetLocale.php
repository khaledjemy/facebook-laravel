<?php
namespace App\Http\Middleware;
use Closure;
class SetLocale {
    public function handle($request, Closure $next) {
        $locale = session('locale', config('app.locale'));
        if (in_array($locale, ['en','ar'], true)) app()->setLocale($locale);
        return $next($request);
    }
}
