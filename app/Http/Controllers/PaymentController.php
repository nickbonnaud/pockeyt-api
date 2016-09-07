<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\User;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    
    public function clientToken() {
        JWTAuth::parseToken()->authenticate();
        $clientToken = \Braintree_ClientToken::generate();
        return response()->json(compact('clientToken'));
    }

    public function createCustomer(Request $request) {
        $authUser = JWTAuth::parseToken()->authenticate();
        $result = \Braintree_Customer::create([
            'firstName' => $authUser->first_name,
            'lastName' => $authUser->last_name,
            'email' => $authUser->email,
            'paymentMethodNonce' => $request->userNonce
        ]);
        if ($result->success) {
            $dbUser = User::findOrFail($authUser->id);
            $dbUser['customer_id'] = $result->customer->id;
            $dbUser->save();
            return $result->customer->paymentMethods();
        } else {
            foreach($result->errors->deepAll() AS $error) {
                return($error->code . ": " . $error->message . "\n");
            }
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
