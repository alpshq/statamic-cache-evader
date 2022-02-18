<?php

namespace Alps\CacheEvader\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SpoofXsrfHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->input('_xsrf_token');

        if ($token) {
            $request->headers->add([
                'x-xsrf-token' => (string) $token,
            ]);
        }

        return $next($request);
    }
}
