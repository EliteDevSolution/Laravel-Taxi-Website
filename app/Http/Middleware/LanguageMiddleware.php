<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Foundation\Application;
use App;
use Session;

class LanguageMiddleware
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
       if (\Auth::check()) {
           $language = \Auth::User()->language;             
           App::setLocale($language);
       }
       return $next($request);
    }
}