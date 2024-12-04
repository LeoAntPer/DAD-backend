<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogHttpRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the HTTP request details
        Log::info('HTTP Request', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        // Proceed with the next middleware or controller
        return $next($request);
    }
}
