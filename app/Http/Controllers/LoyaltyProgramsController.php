<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use App\LoyaltyProgram;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LoyaltyProgramsController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth', []);
        parent::__construct();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loyaltyProgram = $this->user->profile->loyaltyProgram;
        if (isset($loyaltyProgram)) {
            $loyaltyProgramId = $loyaltyProgram->id;
            return view('loyalty-programs.show', compact('loyaltyProgramId'));
        } else {
            return view('loyalty-programs.create');
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $loyaltyProgram = new loyaltyProgram($request->except(['optionsRadios']));
       if ($request->input('optionsRadios') === 'increments') {
            $loyaltyProgram->is_increment = true;
       }
       $this->user->profile->loyaltyProgram()->save($loyaltyProgram);
       return redirect()->route('loyalty-programs.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loyaltyProgram = $this->user->profile->loyaltyProgram;
        return view('loyalty-programs.show', compact('loyaltyProgram'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $account = Account::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

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
