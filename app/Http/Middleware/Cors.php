<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Response as IlluminateResponse;

class Cors {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $response = $next($request);
        if(!($response instanceof Response)) {
            $response = with(new IlluminateResponse())->setContent($response);
        }
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
