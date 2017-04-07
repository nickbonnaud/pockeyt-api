<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\LoyaltyCard;
use App\User;
use JWTAuth;
use App\Post;
use App\PostAnalytic;
use Carbon\Carbon;
use DateTimeZone;
use App\PushId;
use App\Profile;
use App\Product;
use App\Transaction;
use App\Http\Requests;
use App\Events\RewardNotification;
use App\Events\TransactionsChange;
use App\Events\ErrorNotification;
use Illuminate\Support\Facades\DB;
use App\Events\CustomerRequestBill;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\ChargeRequest;
use App\Http\Requests\UpdateChargeRequest;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->middleware('jwt.auth', ['only' => ['UserConfirmBill', 'requestBill', 'userDeclineBill', 'customTip', 'getCurrentBill', 'hasBill', 'getRecentTransactions', 'purchaseDeal']]);
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
        $profile = $this->user->profile;
        $transaction->paid = false;
        $transaction->status = 10;
        $profile->transactions()->save($transaction);
        event(new TransactionsChange($profile));
        return $this->confirmTransaction($transaction, $customer, $profile);
    }

    public function chargeExisting(UpdateChargeRequest $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        $customer = User::findOrFail($transaction->user_id);
        $profile = $this->user->profile;
        $transaction->paid = false;
        $transaction->status = 10;
        $profile->transactions()->save($transaction);
        event(new TransactionsChange($profile));
        return $this->confirmTransaction($transaction, $customer, $profile);
    }

    public function confirmTransaction($transaction, $customer, $profile) {
        $subTotal = round(($transaction->tax + $transaction->net_sales) / 100, 2);
        $message = \PushNotification::Message('Please swipe left or down to view bill and pay. You have been charged $' . $subTotal . ' by ' . $profile->business_name . '.', array(
          'category' => 'payment',
          'locKey' => '1',
          'custom' => array('transactionId' => $transaction->id,
                            'businessId' => $profile->id,
                            'inAppMessage' => 'You have been charged $' . $subTotal . ' by ' . $profile->business_name
                        )
        ));
        $token = PushId::where('user_id', '=', $customer->id)->first();
        if ($token->device_type === 'iOS') {
            $pushService = 'PockeytIOS';
        } else {
            $pushService = 'PockeytAndroid';
        }
        $collection = \PushNotification::app($pushService)
          ->to($token->push_token)
          ->send($message);

        foreach ($collection->pushManager as $push) {
          $response = $push->getAdapter()->getResponse()->getCode();
        }

        if ($response === 0) {
            $transaction->status = 11;
            $profile->transactions()->save($transaction);
            return $this->flashSuccessPush($customer, $profile);
        } else {
            $transaction->status = 0;
            $profile->transactions()->save($transaction);
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
        $profile = Profile::findOrFail($transaction->profile_id);

        if ($customer->id === $transaction->user_id && !$transaction->paid) {
            $total = $transaction->total;
            $tip = ($customer->default_tip_rate / 100) * $total;

            $transaction->tips = round($tip);
            $transaction->total = round($total + $tip);
            $transaction->save();
            $result = $this->createCharge($transaction, $customer, $profile->id);

            if ($result->success) {
                $transaction->paid = true;
                $transaction->status = 20;
                $transaction->save();
                event(new TransactionsChange($profile));
                $this->checkRecentViewedPosts($customer, $profile, $transaction);
                $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
                return $this->updateLoyaltyCard($newLoyaltyCard, $customer, $profile);
            } else {
                $transaction->paid = false;
                $transaction->status = 1;
                $transaction->save();
                event(new TransactionsChange($profile));
                return event(new ErrorNotification($customer, $profile, $transaction));
            }
        }
    }

    public function userDeclineBill(Request $request) {
        $customer = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::findOrFail($request->transactionId);
        $profile = Profile::findOrFail($transaction->profile_id);

        if ($customer->id === $transaction->user_id && !$transaction->paid) {
            $transaction->paid = false;
            $transaction->status = 2;
            $transaction->save();
            event(new TransactionsChange($profile));
            return event(new ErrorNotification($customer, $profile, $transaction));
        } else {
            return;
        }
    }

    public function customTip(Request $request) {
        $customer = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::findOrFail($request->transactionId);
        $profile = Profile::findOrFail($transaction->profile_id);

        if ($request->tipSet === true) {
            if ($customer->id === $transaction->user_id && !$transaction->paid) {
                $transaction->tips = round($request->tips * 100);
                $transaction->total = round($request->total * 100);
                $transaction->save();
                $result = $this->createCharge($transaction, $customer, $profile->id);

                if ($result->success) {
                    $transaction->paid = true;
                    $transaction->status = 20;
                    $transaction->save();
                    event(new TransactionsChange($profile));
                    $this->checkRecentViewedPosts($customer, $profile, $transaction);
                    $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
                    return $this->updateLoyaltyCard($newLoyaltyCard, $customer, $profile);
                } else {
                    $transaction->paid = false;
                    $transaction->status = 1;
                    $transaction->save();
                    event(new TransactionsChange($profile));
                    return event(new ErrorNotification($customer, $profile, $transaction));
                }
            }
        } else {
            $profile['logo_photo'] = $profile->logo->thumbnail_url;

            if ($customer->id === $transaction->user_id && !$transaction->paid) {
                return response()->json(array('customer' => $customer, 'transaction' => $transaction, 'profile' => $profile));
            } else { 
                return response()->json(['error' => 'Unable to retrieve transaction.'], 404);
            }
        }
    }

    public function getCurrentBill() {
        $customer = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::where(function ($query) use ($customer) {
            $query->where('user_id', '=', $customer->id)
                ->where('paid', '=', false);
        })->first();
        if (isset($transaction)) {
            $profile = Profile::findOrFail($transaction->profile_id);
            $profile['logo_photo'] = $profile->logo->thumbnail_url;
            return response()->json(array('customer' => $customer, 'transaction' => $transaction, 'profile' => $profile));
        } else {
            return response()->json('noBill', 404);
        }
    }

    public function getSentTransaction() {
        $customer = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::where(function ($query) use ($customer) {
            $query->where('user_id', '=', $customer->id)
                ->where('paid', '=', false)
                ->where('status', '=', 11);
        })->first();
        if (!$transaction) {
            return response()->json(['noBill' => 'No Open Bills'], 200);
        } else {
            $profile = Profile::findOrFail($transaction->profile_id);
            $profile['logo_photo'] = $profile->logo->thumbnail_url;
            return response()->json(array('customer' => $customer, 'transaction' => $transaction, 'profile' => $profile));
        }
    }

    public function hasBill() {
        $customer = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::where(function ($query) use ($customer) {
            $query->where('user_id', '=', $customer->id)
                ->where('paid', '=', false);
        })->first();
        if (!$transaction) {
            return response()->json(false, 200);
        } else {
            if ($transaction->status == 11) {
                return response()->json(['billSent' => true], 200);
            } else {
                return response()->json(['billSent' => false], 200);
            }
        }
    }

    public function purchaseDeal(Request $request) {
        $customer = JWTAuth::parseToken()->authenticate();
        $profile = Profile::findOrFail($request->business_id);

        $transaction = new Transaction;

        $transaction->profile_id = $profile->id;
        $transaction->user_id = $customer->id;
        $transaction->paid = false;
        $transaction->deal_id = $request->id;
        $transaction->net_sales = $request->price;

        $tax = round(($profile->tax_rate / 10000) * $transaction->net_sales);
        $total = $transaction->net_sales + $tax;
        $transaction->tax = $tax;
        $transaction->total = $total;

        $profile->transactions()->save($transaction);
        $result = $this->createCharge($transaction, $customer, $profile->id);

        if ($result->success) {
            $transaction->paid = true;
            $transaction->status = 20;
            $transaction->save();

            return response()->json(['success' => 'Post Purchased'], 200);
        } else {
            $transaction->paid = false;
            $transaction->status = 1;
            $transaction->save();
            return response()->json(['error' => 'Unable to charge card.'], 400);
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

    public function updateLoyaltyCard($newLoyaltyCard, $customer, $profile) {
        if (isset($newLoyaltyCard)) {
            $loyaltyProgram = $profile->loyaltyProgram;
            if ($newLoyaltyCard->transactionRewards > 0) {
                return event(new RewardNotification($customer, $profile, $loyaltyProgram));
            } else {
                return;
            }
        } else {
            return;
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
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subDays(7);
        $customerData = [];

        $purchases = Transaction::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('profile_id', '=', $businessId);
        })->orderBy('updated_at', 'desc')->take(5)->get();

        $lastPostViewed = PostAnalytic::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('business_id', '=', $businessId);
        })->orderBy('updated_at', 'desc')->first();
        if (isset($lastPostViewed)) {
            array_push($customerData, $lastPostViewed);
        } else {
            $lastPostViewed = "none";
            array_push($customerData, $lastPostViewed);
        }

        $recentShared = PostAnalytic::where(function($query) use ($fromDate, $currentDate, $customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('business_id', '=', $businessId)
                ->whereBetween('shared_on', [$fromDate, $currentDate]);
        })->orderBy('shared_on', 'desc')->first();
        if (isset($recentShared)) {
            array_push($customerData, $recentShared);
        } else {
            $recentShared = "none";
            array_push($customerData, $recentShared);
        }

        $recentBookmarked = PostAnalytic::where(function($query) use ($fromDate, $currentDate, $customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('business_id', '=', $businessId)
                ->whereBetween('bookmarked_on', [$fromDate, $currentDate]);
        })->orderBy('bookmarked_on', 'desc')->first();
        if (isset($recentBookmarked)) {
            array_push($customerData, $recentBookmarked);
        } else {
            $recentBookmarked = "none";
            array_push($customerData, $recentBookmarked);
        }

        $customerData = collect($customerData);
        if(isset($purchases)) {
            return response()->json($purchases, $customerData);
        } else {
            return response()->json($customerData);
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

    public function getTransactions(Request $request) {
        $businessId = $request->businessId;
        $transactionsPending = Transaction::where(function($query) use ($businessId) {
            $query->where('profile_id', '=', $businessId)
                ->where('status', '<', '20');
        })->orderBy('status', 'asc')->get();

        $transactionsFinalized = Transaction::where(function($query) use ($businessId) {
            $query->where('profile_id', '=', $businessId)
                ->where('status', '>=', '20');
        })->orderBy('updated_at', 'desc')->take(10)->get();

        foreach ($transactionsPending as $transaction) {
            $customer = User::findOrFail($transaction->user_id);
            $transaction['customerName'] = $customer->first_name . ' ' . $customer->last_name;
        }

        foreach ($transactionsFinalized as $transaction) {
            $customer = User::findOrFail($transaction->user_id);
            $transaction['customerName'] = $customer->first_name . ' ' . $customer->last_name;
        }

        return response()->json(array('transactionsPending' => $transactionsPending, 'transactionsFinalized' => $transactionsFinalized));
    }

    public function requestBill(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $transaction = Transaction::findOrFail($request->transactionId);
        $business = Profile::findOrFail($transaction->profile_id);
        return event(new CustomerRequestBill($user, $business));
    }

    public function checkRecentViewedPosts($customer, $profile, $transaction) {
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subDays(2);
        
        $postViewed = PostAnalytic::where(function($query) use ($fromDate, $currentDate, $profile, $customer) {
          $query->whereBetween('updated_at', [$fromDate, $currentDate])
            ->where('user_id', '=', $customer->id)
            ->where('business_id', '=', $profile->id);
        })->orderBy('viewed_on', 'desc')->take(1)->get();

        if (count($postViewed) !== 0) {
            $transactionRevenue = $transaction->tips + $transaction->net_sales;
            foreach ($postViewed as $viewed) {
                $viewed->transaction_resulted = true;
                $viewed->transaction_on = Carbon::now(new DateTimeZone(config('app.timezone')));
                $prevPurchases = $viewed->total_revenue;
                $viewed->total_revenue = $prevPurchases + $transactionRevenue;
                $viewed->save();

                $post = Post::findOrFail($viewed->post_id);
                $postRevenue = $post->total_revenue;
                $post->total_revenue = $postRevenue + $transactionRevenue;
                $post->save();
            }
        }
        return;
    }

    public function getRecentTransactions() {
        $customer = JWTAuth::parseToken()->authenticate();
        $paginator = Transaction::where(function($query) use ($customer) {
            $query->where('user_id', '=', $customer->id)
                ->where('paid', '=', true);
        })->orderBy('updated_at', 'desc')->paginate(10);

        $transactions = $paginator->getCollection();
        return fractal()
            ->collection($transactions, function(Transaction $transaction) {
                    return [
                        'id' => $transaction->id,
                        'business_logo' => $transaction->profile->logo->thumbnail_url,
                        'business_name' => $transaction->profile->business_name,
                        'deal_id' => $transaction->deal_id,
                        'redeemed' => $transaction->redeemed,
                        'products' => $transaction->products,
                        'tax' => $transaction->tax,
                        'tips' => $transaction->tips,
                        'net_sales' => $transaction->net_sales,
                        'total' => $transaction->total,
                        'updated_at' => $transaction->updated_at,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
    }

    public function getDeals(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $paginator = Transaction::with([])
            ->join('posts', function($join) use ($user) {
                $join->on('transactions.deal_id', '=', 'posts.id')
                    ->where('transactions.user_id', '=', $user->id);
            })
            ->orderBy('redeemed', 'asc')->paginate(10);

            $transactions = $paginator->getCollection();
            return fractal()
                ->collection($transactions, function(Transaction $transaction) {
                        return [
                            'deal_id' => $transaction->deal_id,
                            'deal_item' => $transaction->deal_item,
                            'end_date' => $transaction->end_date,
                            'message' => $transaction->message,
                            'post_photo_path' => $transaction->photo_path,
                            'price' => $transaction->price,
                            'business_id' => $transaction->profile_id,
                            'redeemed' => $transaction->redeemed,
                            'business_thumb_path' => $transaction->profile->logo->thumbnail_url,
                            'business_name' => $transaction->profile->business_name,
                            'tax' => $transaction->tax,
                            'total' => $transaction->total,
                            'customer_id' => $transaction->user_id,
                            'purchased_on' => $transaction->updated_at
                        ];
                })
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }
}







