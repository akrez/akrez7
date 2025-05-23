<?php

namespace App\Http\Middleware;

use App\Services\DomainService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDomainBlogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->route()->parameter('domain');

        $request->merge([
            'blog_id' => DomainService::new()->domainToBlogId($domain)->abortUnSuccessful()->getData('blog_id'),
        ]);

        return $next($request);
    }
}
