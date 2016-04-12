<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate {
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $level = 'user') {
        if($this->auth->guest()) {
            if($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('auth.login'));
            }
        } elseif($level === 'admin' && !$this->auth->user()->is_admin) {
            if($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->to('/');
            }
        } elseif(
            // If user is not admin...
            !$this->auth->user()->is_admin &&
            // ... and does not have a profile ...
            is_null($this->auth->user()->profile) &&
            // ... and the request is properly routed ...
            !is_null($route = $request->route()) &&
            // ... and they're not actively trying to create a profile...
            ($route->getName() !== 'profiles.create' && $route->getName() !== 'profiles.store')
            // ... then redirect them to the "Create Profile" form.
        ) {
            if($request->ajax()) {
                return response('Must create profile first.', 400);
            } else {
                return redirect()->route('profiles.create');
            }
        }

        return $next($request);
    }
}
