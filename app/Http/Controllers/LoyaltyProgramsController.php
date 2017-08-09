<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use App\Profile;
use App\LoyaltyProgram;
use App\LoyaltyCard;
use Illuminate\Http\Request;
use App\Http\Requests\LoyaltyProgramRequest;
use App\Http\Requests\DeleteLoyaltyProgramRequest;
use App\Http\Requests;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Http\Controllers\Controller;

class LoyaltyProgramsController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth', ['except' => ['getLoyaltyCards']]);
        $this->middleware('jwt.auth', ['only' => ['getLoyaltyCards']]);
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
            return view('loyalty-programs.show', compact('loyaltyProgram'));
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
    public function store(LoyaltyProgramRequest $request)
    {
       $loyaltyProgram = new loyaltyProgram($request->except(['optionsRadios']));
       if ($request->input('optionsRadios') === 'increments') {
            $loyaltyProgram->is_increment = true;
       } else {
            $loyaltyProgram->is_increment = false;
            $loyaltyProgram->amount_required = preg_replace("/[^0-9\.]/","",$loyaltyProgram->amount_required) * 100;
       }
       $loyaltyProgram->reward = lcfirst($loyaltyProgram->reward);
       $this->user->profile->loyaltyProgram()->save($loyaltyProgram);
       return redirect()->route('loyalty-programs.show', compact('loyaltyProgram'));
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteLoyaltyProgramRequest $request, $id)
    {
        $loyaltyProgram = LoyaltyProgram::findOrFail($id);
        $loyaltyCards = LoyaltyCard::where('program_id', '=', $loyaltyProgram->id)->get();
        foreach ($loyaltyCards as $loyaltyCard) {
            $loyaltyCard->delete();
        }
        $loyaltyProgram->delete();
        return redirect()->route('loyalty-programs.create');
    }

    public function getLoyaltyCards(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();

        $paginator = LoyaltyProgram::with([])
            ->join('loyalty_cards', function($join) use ($user) {
                $join->on('loyalty_programs.id', '=', 'loyalty_cards.program_id')
                    ->where('loyalty_cards.user_id', '=', $user->id);
            })
            ->orderBy('loyalty_cards.updated_at', 'desc')->paginate(10);
            $loyaltyCards = $paginator->getCollection();
            return fractal()
                ->collection($loyaltyCards, function(LoyaltyProgram $loyaltyCard) {
                        return [
                            'program_id' => $loyaltyCard->program_id,
                            'current_amount' => $loyaltyCard->current_amount,
                            'rewards_achieved' => $loyaltyCard->rewards_achieved,
                            'is_increment' => $loyaltyCard->is_increment,
                            'purchases_required' => $loyaltyCard->purchases_required,
                            'amount_required' => $loyaltyCard->amount_required,
                            'reward' => $loyaltyCard->reward,
                            'business_thumb_path' => $loyaltyCard->profile->logo->thumbnail_url,
                            'business_name' => $loyaltyCard->profile->business_name,
                            'last_purchase' => $loyaltyCard->updated_at
                        ];
                })
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();

    }

}
