<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set the application's language based on the request header
 *
 * This checks the 'Accept-Language' header from the incoming request
 * If the header isn't provided, it defaults to English ('en')
 */
class SetLocaleLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the locale from the 'Accept-Language' header, or default to 'en'
        $locale = $request->header('Accept-Language', 'en');

        // Set the application's locale for the current request
        app()->setLocale($locale);

        return $next($request);
    }
}
