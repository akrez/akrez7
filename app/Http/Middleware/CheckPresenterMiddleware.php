<?php

namespace App\Http\Middleware;

use App\Services\PresentService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPresenterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $presenter): Response
    {
        $presentInfo = PresentService::new()->makePresentInfo(
            Auth::id(),
            $presenter,
            $request->route()->parameter('blog_id'),
            $request->route()->parameter('domain')
        );

        abort_unless($presentInfo, 404);

        $request->merge($presentInfo);

        return $next($request);
    }
}
