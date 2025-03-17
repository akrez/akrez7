<?php

namespace App\Http\Middleware;

use App\Support\ActiveBlog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveBlogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app(ActiveBlog::class)->has()) {
            return redirect()->route('blogs.index');
        }

        return $next($request);
    }
}
