<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\LoyaltyCard;
use App\User;
use JWTAuth;
use App\Post;
use App\Product;
use App\Transaction;
use App\Http\Requests;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\ChargeRequest;
use App\Http\Requests\UpdateChargeRequest;

use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
     public function __construct()
    {
        parent::__construct();
        $this->middleware('jwt.auth', ['only' => ['UserConfirmBill']]);
    }

    public function showBill($customerId) {
        $customer = User::findOrFail($customerId);
        $business = $this->user->profile;
        $inventory = Product::where('profile_id', '=', $business->id)->get();
        $transaction = Transaction::where(function($query) use ($customer, $business) {
            $query->where('user_id', '=', $customer->id)
                ->where('profile_id', '=', $business->id)
                ->where('paid', '=', false);
        })->first();
        $locationCheck = $this->userInLocationCheck($customer, $business);
        if(isset($transaction) && isset($locationCheck)) {
            $bill = $transaction->products;
            $billId = $transaction->id;
            return view('transactions.bill_show', compact('customer', 'business', 'inventory', 'bill', 'billId'));
        } elseif(isset($locationCheck)) {
            return view('transactions.bill_create', compact('customer', 'business', 'inventory'));
        } else {
            flash()->error('Oops', 'Customer not in business radius!');
            return redirect()->route('profiles.show', ['profiles' => $business->id]);
        }
    }
    public function userInLocationCheck($customer, $business) {
        $locationCheck = Location::where(function ($query) use ($customer, $business) {
            $query->where('user_id', '=', $customer->id)
                ->where('location_id', '=', $business->id);
        })->first();
        return $locationCheck;
    }

    public function store(TransactionRequest $request) {
        $transaction = new Transaction($request->all());
        $profile = $this->user->profile;
        $profile->transactions()->save($transaction);

        return view('profiles.show', compact('profile'));
    }

    public function update(UpdateTransactionRequest $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        $profile = $this->user->profile;

        return view('profiles.show', compact('profile'));
    }

    public function charge(ChargeRequest $request) {
        $transaction = new Transaction($request->all());
        $customer = User::findOrFail($transaction->user_id);
        if (isset($customer->default_tip_rate)) {
            $tip = $transaction->total * ($customer->default_tip_rate / 10000);
            $transaction->total = $transaction->total + $tip;
            $transaction->tips = $tip;
        }
        $profile = $this->user->profile;
        $transaction->paid = false;
        $profile->transactions()->save($transaction);

        return $this->confirmTransaction($transaction, $customer, $profile);

        $result = $this->createCharge($transaction, $customer, $profile);

        if ($result->success) {
            $transaction->paid = true;
            $profile->transactions()->save($transaction);
            $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
            return $this->flashMessage($newLoyaltyCard, $customer, $profile);
        } else {
            $transaction->paid = false;
            $profile->transactions()->save($transaction);
            $bill = $transaction->products;
            $billId = $transaction->id;
            $inventory = Product::where('profile_id', '=', $profile->id)->get();
            
            return view('transactions.bill_show', compact('customer', 'inventory', 'bill', 'billId'))
                ->withErrors($result->errors->deepAll());
        }
    }

    public function chargeExisting(UpdateChargeRequest $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        $customer = User::findOrFail($transaction->user_id);
        if (isset($customer->default_tip_rate)) {
            $tip = $transaction->total * ($customer->default_tip_rate / 10000);
            $transaction->total = $transaction->total + $tip;
            $transaction->tips = $tip;
        }
        $profile = $this->user->profile;
        $transaction->paid = false;
        $profile->transactions()->save($transaction);

        return $this->confirmTransaction($transaction, $customer, $profile);

        $result = $this->createCharge($transaction, $customer, $profile);

        if ($result->success) {
            $transaction->paid = true;
            $transaction->save();
            $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
            return $this->flashMessage($newLoyaltyCard, $customer, $profile);
        } else {
            $transaction->paid = false;
            $transaction->save();
            $bill = $transaction->products;
            $billId = $transaction->id;
            $inventory = Product::where('profile_id', '=', $profile->id)->get();
            
            return view('transactions.bill_show', compact('customer', 'inventory', 'bill', 'billId'))
                ->withErrors($result->errors->deepAll());
        }
    }

    public function confirmTransaction($transaction, $customer, $profile) {
        $subTotal = round(($transaction->tax + $transaction->net_sales) / 100, 2);
        $message = \PushNotification::Message('You have been charged $' . $subTotal . ' by ' . $profile->business_name, array(
          'category' => 'payment',
          'locKey' => '1',
          'custom' => array('transactionId' => $transaction->id)
        ));
        $collection = \PushNotification::app('PockeytIOS')
          ->to('ac201490161a80f0bea413f55911473108cf6b57cab5489b3fda6c169cd731ae')
          ->send($message);

        foreach ($collection->pushManager as $push) {
          $response = $push->getAdapter()->getResponse()->getCode();
        }

        if ($response === 0) {
            return $this->flashSuccessPush($customer, $profile);
        } else {
            return $this->flashErrorPush($profile);
        }
    }

    public function flashSuccessPush($customer, $profile) {
        flash()->success('Bill created!', 'Waiting for ' . $customer->first_name . ' to approve bill');
        return redirect()->route('profiles.show', ['profiles' => $profile->id]);
    }

    public function flashErrorPush($profile) {
        flash()->overlay('Oops!', 'An error occurred while sending Bill to the customer. Please contact customer support.', 'error');
        return redirect()->route('profiles.show', ['profiles' => $profile->id]);
    }

    public function userConfirmBill(Request $request) {
        $customer = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::findOrFail($request->transactionId);
        $profileId = $transaction->profile_id;

        if ($customer->id === $transaction->user_id && !$transaction->paid) {
            $result = $this->createCharge($transaction, $customer, $profileId);

            if ($result->success) {
                $transaction->paid = true;
                $transaction->save();
                $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
                return $this->flashMessage($newLoyaltyCard, $customer, $profile);
            } else {
                $transaction->paid = false;
                $transaction->save();
                $bill = $transaction->products;
                $billId = $transaction->id;
                $inventory = Product::where('profile_id', '=', $profile->id)->get();
                
                return view('transactions.bill_show', compact('customer', 'inventory', 'bill', 'billId'))
                    ->withErrors($result->errors->deepAll());
            }
        }
    }

    private function createCharge($transaction, $customer, $profileId) {
        $amount = (round($transaction->total)) / 100;
        $serviceFee = round($amount * 0.02, 2);
        $result = \Braintree_Transaction::sale([
            'merchantAccountId' => $profileId,
            'amount' => $amount,
            'customerId' => $customer->customer_id,
            'customer' => [
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
                'email' => $customer->email,
            ],
            'serviceFeeAmount' => $serviceFee,
            'options' => [
                'submitForSettlement' => True
            ]
        ]);

        return $result;
    }

    public function checkLoyaltyProgram($customer, $profile, $transaction) {
        $loyaltyProgram = $profile->loyaltyProgram;
        if(isset($loyaltyProgram)) {
            return $this->getCustomerLoyaltyCard($customer, $loyaltyProgram, $transaction);
        } else {
            return;
        }
    }

    public function getCustomerLoyaltyCard($customer, $loyaltyProgram, $transaction) {
        $loyaltyCard = LoyaltyCard::where(function ($query) use ($customer, $loyaltyProgram) {
            $query->where('user_id', '=', $customer->id)
                ->where('program_id', '=', $loyaltyProgram->id);
        })->first();

        if (isset($loyaltyCard)) {
            return $this->addToLoyaltyCard($loyaltyCard, $loyaltyProgram, $transaction);
        } else {
            return $this->createLoyaltyCard($customer, $loyaltyProgram, $transaction);
        }
    }

    public function addToLoyaltyCard($loyaltyCard, $loyaltyProgram, $transaction) {
        if($loyaltyProgram->is_increment) {
            if(($loyaltyCard->current_amount + 1) === $loyaltyProgram->purchases_required) {
                $loyaltyCard->current_amount = 0;
                $loyaltyCard->rewards_achieved = $loyaltyCard->rewards_achieved + 1;
                $loyaltyCard->save();
                $loyaltyCard['transactionRewards'] = 1;
                $loyaltyCard['type'] = 'increment';
                $loyaltyCard['required'] = $loyaltyProgram->purchases_required;
                return $loyaltyCard;
            } else {
                $loyaltyCard->current_amount = $loyaltyCard->current_amount + 1;
                $loyaltyCard->save();
                $loyaltyCard['transactionRewards'] = 0;
                $loyaltyCard['type'] = 'increment';
                $loyaltyCard['required'] = $loyaltyProgram->purchases_required;
                return $loyaltyCard;
            }
        } else {
            if(($loyaltyCard->current_amount + $transaction->total) >= $loyaltyProgram->amount_required) {
                $prevRewardsAchieved = $loyaltyCard->rewards_achieved;
                $loyaltyCard->current_amount = $loyaltyCard->current_amount + $transaction->total;
                while($loyaltyCard->current_amount >= $loyaltyProgram->amount_required) {
                    $loyaltyCard->rewards_achieved = $loyaltyCard->rewards_achieved + 1;
                    $loyaltyCard->current_amount = $loyaltyCard->current_amount - $loyaltyProgram->amount_required;
                }
                $loyaltyCard->save();
                $loyaltyCard['transactionRewards'] = $loyaltyCard->rewards_achieved - $prevRewardsAchieved;
                $loyaltyCard['type'] = 'amount';
                $loyaltyCard['required'] = $loyaltyProgram->amount_required;
                return $loyaltyCard;
            } else {
                $loyaltyCard->current_amount = $loyaltyCard->current_amount + $transaction->total;
                $loyaltyCard->save();
                $loyaltyCard['transactionRewards'] = 0;
                $loyaltyCard['type'] = 'amount';
                $loyaltyCard['required'] = $loyaltyProgram->amount_required;
                return $loyaltyCard;
            }
        }
    }

    public function createLoyaltyCard($customer, $loyaltyProgram, $transaction) {
        $loyaltyCard = new LoyaltyCard;
        if($loyaltyProgram->is_increment) {
            $loyaltyCard->program_id = $loyaltyProgram->id;
            $loyaltyCard->current_amount = 1;
            $loyaltyCard->rewards_achieved = 0;
            $customer->loyaltyCards()->save($loyaltyCard);
            $loyaltyCard['transactionRewards'] = 0;
            $loyaltyCard['type'] = 'increment';
            $loyaltyCard['required'] = $loyaltyProgram->purchases_required;
            return $loyaltyCard;
        } else {
            $loyaltyCard->program_id = $loyaltyProgram->id;
            $loyaltyCard->current_amount = $transaction->total;
            $loyaltyCard->rewards_achieved = 0;
            if($loyaltyCard->current_amount >= $loyaltyProgram->amount_required) {
                while($loyaltyCard->current_amount >= $loyaltyProgram->amount_required) {
                    $loyaltyCard->rewards_achieved = $loyaltyCard->rewards_achieved + 1;
                    $loyaltyCard->current_amount = $loyaltyCard->current_amount - $loyaltyProgram->amount_required;
                }
            }
            $customer->loyaltyCards()->save($loyaltyCard);
            $loyaltyCard['transactionRewards'] = $loyaltyCard->rewards_achieved;
            $loyaltyCard['type'] = 'amount';
            $loyaltyCard['required'] = $loyaltyProgram->amount_required;
            return $loyaltyCard;

        }
    }

    public function flashMessage($newLoyaltyCard, $customer, $profile) {
        if (isset($newLoyaltyCard)) {
            $reward = $profile->loyaltyProgram->reward;
            if (($newLoyaltyCard->transactionRewards === 1) && ($newLoyaltyCard->type === "increment")) {
                flash()->overlay($customer->first_name . ' gets a ' . $reward, $customer->first_name . ' has completed ' . $newLoyaltyCard->required . ' transactions!');
            } elseif (($newLoyaltyCard->transactionRewards === 1) && ($newLoyaltyCard->type === "amount")) {
                flash()->overlay($customer->first_name . ' gets a ' . $reward, $customer->first_name . ' has purchased $' . ($newLoyaltyCard->required / 100) . ' here!');
            } elseif (($newLoyaltyCard->transactionRewards > 1) && ($newLoyaltyCard->type === "amount")) {
                flash()->overlay($customer->first_name . ' gets ' . $newLoyaltyCard->transactionRewards . ' ' . str_plural($reward), $customer->first_name . ' has purchased $' . ((($newLoyaltyCard->transactionRewards) * ($newLoyaltyCard->required)) / 100) . ' here!');
            } else{
                flash()->success('Paid', 'Transaction Complete');
            }
            return redirect()->route('profiles.show', ['profiles' => $profile->id]);
        } else {
            flash()->success('Paid', 'Transaction Complete');
            return redirect()->route('profiles.show', ['profiles' => $profile->id]);
        }
    }

    public function getPurchased(Request $request) {
        $postId = $request->postId;
        $purchased = Transaction::where('deal_id', '=', $postId)->get();

        return response()->json($purchased);
    }

     public function getUserPurchases(Request $request) {
        $customerId = $request->customerId;
        $businessId = $request->businessId;

        $purchases = Transaction::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('profile_id', '=', $businessId);
        })->orderBy('updated_at', 'desc')->take(5)->get();

        if(isset($purchases)) {
            return response()->json($purchases);
        } else {
            return;
        }
    }

    public function getUserDeals(Request $request) {
        $customerId = $request->customerId;
        $businessId = $request->businessId;

        $redeemableDeals = Transaction::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('profile_id', '=', $businessId)
                ->where('redeemed', '=', false);     
        })->get();
        if (isset($redeemableDeals)) {
            return response()->json($redeemableDeals);  
        } else {
            return;
        }
    }

    public function redeemUserDeal(Request $request) {
        $transactionId = $request->dealId;
        $transaction = Transaction::findOrFail($transactionId);

        $transaction->redeemed = true;
        $transaction->save();
        return response('success');
    }

}







