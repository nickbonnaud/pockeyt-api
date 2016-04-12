<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController {

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The user instance
     * @var User
     */
    protected $user;

    public function __construct() {
        $this->user = \Auth::user();

        view()->share('signedIn', \Auth::check());
        view()->share('hasProfile', \Auth::check() && !is_null(\Auth::user()->profile));
        view()->share('isAdmin', \Auth::check() && \Auth::user()->is_admin);
        view()->share('user', $this->user);
    }
}
