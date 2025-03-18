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
        $activeBlog =  app(ActiveBlog::class);

        $activeBlog->set($request->user());

        if (! $activeBlog->has()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
