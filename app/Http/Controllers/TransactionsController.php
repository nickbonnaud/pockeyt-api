<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    
    public function createTransaction($customerId) {
        dd($customerId);
    }

}