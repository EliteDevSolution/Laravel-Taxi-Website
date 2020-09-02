<?php

namespace App\Http\Middleware;

use Closure;

class DemoModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', trans('admin.demomode'));
        }
        return $next($request);
    }
}
