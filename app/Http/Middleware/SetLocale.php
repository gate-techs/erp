<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userLocale = auth()->check() ? auth()->user()->locale : null;
        $locale = Session::get('locale', $userLocale ?: config('app.locale', 'ar'));

        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        } else {
            App::setLocale('ar');
        }

        return $next($request);
    }
}
