<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function createTransaction($customerId) {
        dd($customerId);
    }

}