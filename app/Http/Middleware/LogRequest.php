<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        info(implode(' ', [
            str_pad('🟢'.($request->user() ? $request->user()->id : ''), 8),
            str_pad($request->method(), 8),
            $request->fullUrl(),
        ]));

        return $response;
    }
}
