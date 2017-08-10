<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\LoyaltyCard;
use App\User;
use JWTAuth;
use App\Post;
use App\Account;
use App\PostAnalytic;
use Carbon\Carbon;
use DateTimeZone;
use App\PushId;
use App\Profile;
use Mail;
use Crypt;
use App\Product;
use App\Transaction;
use App\Http\Requests;
use App\Events\RewardNotification;
use App\Events\TransactionsChange;
use App\Events\ErrorNotification;
use App\Events\PaymentSuccessNotification;
use Illuminate\Support\Facades\DB;
use App\Events\CustomerRequestBill;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\ChargeRequest;
use App\Http\Requests\UpdateChargeRequest;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use SplashPayments;
use App\Http\Controllers\Controller;

class TransactionsController extends Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->middleware('jwt.auth', ['only' => ['UserConfirmBill', 'requestBill', 'userDeclineBill', 'customTip', 'getCurrentBill', 'hasBill', 'getRecentTransactions', 'purchaseDeal']]);
    }

    public function showBill($customerId, $employeeId) {
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
            return view('transactions.bill_create', compact('customer', 'business', 'inventory', 'employeeId'));
        } else {
            flash()->error('Oops', 'Customer not in business radius!');
            return redirect()->route('profiles.show', ['profiles' => Crypt::encrypt($business->id)]);
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
        if ($request->employee_id == 'empty') {
            $transaction->employee_id = null;
        }
        $profile = $this->user->profile;
        $profile->transactions()->save($transaction);

        return redirect()->route('profiles.show', ['profiles' => Crypt::encrypt($profile->id)]);
    }

    public function update(UpdateTransactionRequest $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        $profile = $this->user->profile;

        return redirect()->route('profiles.show', ['profiles' => Crypt::encrypt($profile->id)]);
    }

    public function charge(ChargeRequest $request) {
        $transaction = new Transaction($request->all());
        if ($request->employee_id == 'empty') {
            $transaction->employee_id = null;
        }
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
        $token = PushId::where('user_id', '=', $customer->id)->first();

        if ($token->device_type === 'iOS') {
            $message = \PushNotification::Message('Please swipe left or down to view bill and pay. You have been charged $' . $subTotal . ' by ' . $profile->business_name . '.', array(
              'category' => 'payment',
              'locKey' => '1',
              'custom' => array('transactionId' => $transaction->id,
                                'businessId' => $profile->id,
                                'inAppMessage' => 'You have been charged $' . $subTotal . ' by ' . $profile->business_name
                            )
            ));
            $pushService = 'PockeytIOS';
        } else {
            $message = \PushNotification::Message('You have been charged $' . $subTotal . ' by ' . $profile->business_name . '. Please swipe down if payment options not visible.', array(
              'title' => 'Pockeyt Payment',
              'category' => 'payment',
              'locKey' => '1',
              'actions' => array(
                            (object) array('title' => 'CONFIRM', 'callback' => "window.acceptCharge", "foreground" => true),
                            (object) array('title' => 'REJECT', 'callback' => "window.declineCharge", "foreground" => true),
                            (object) array('title' => 'CUSTOM TIP', 'callback' => "window.changeTip", "foreground" => true),
                            ),
              'custom' => array(
                                'transactionId' => $transaction->id,
                                'businessId' => $profile->id,
                                'inAppMessage' => 'You have been charged $' . $subTotal . ' by ' . $profile->business_name
                            )
            ));
            $pushService = 'PockeytAndroid';
        }
        $collection = \PushNotification::app($pushService)
          ->to($token->push_token)
          ->send($message);

        foreach ($collection->pushManager as $push) {
          $response = $push->getAdapter()->getResponse();
        }
        if ($pushService == 'PockeytIOS') {
            if ($response->getCode() === 0) {
                $transaction->status = 11;
                $profile->transactions()->save($transaction);
                return $this->flashSuccessPush($customer, $profile);
            } else {
                $transaction->status = 0;
                $profile->transactions()->save($transaction);
                return $this->flashErrorPush($profile);
            }
        } else {
            if ($response->getSuccessCount() === 1) {
                $transaction->status = 11;
                $profile->transactions()->save($transaction);
                return $this->flashSuccessPush($customer, $profile);
            } else {
                $transaction->status = 0;
                $profile->transactions()->save($transaction);
                return $this->flashErrorPush($profile);
            }
        }
    }

    public function flashSuccessPush($customer, $profile) {
        flash()->success('Bill created!', 'Waiting for ' . $customer->first_name . ' to approve bill');
        return redirect()->route('profiles.show', ['profiles' => Crypt::encrypt($profile->id)]);
    }

    public function flashErrorPush($profile) {
        flash()->overlay('Oops!', 'An error occurred while sending Bill to the customer. Please contact customer support.', 'error');
        return redirect()->route('profiles.show', ['profiles' => Crypt::encrypt($profile->id)]);
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
            $result = $this->createCharge($transaction, $customer, $profile);

            if ($result) {
                $this->checkFirstTransaction($customer, $transaction);
                $transaction->paid = true;
                $transaction->status = 20;
                $transaction->save();
                event(new TransactionsChange($profile));
                event(new PaymentSuccessNotification($customer, $profile));
                $this->checkRecentViewedPosts($customer, $profile, $transaction);
                $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
                $this->sendEmailReceipt($customer, $profile, $transaction);
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
                $result = $this->createCharge($transaction, $customer, $profile);

                if ($result) {
                    $this->checkFirstTransaction($customer, $transaction);
                    $transaction->paid = true;
                    $transaction->status = 20;
                    $transaction->save();
                    event(new TransactionsChange($profile));
                    event(new PaymentSuccessNotification($customer, $profile));
                    $this->checkRecentViewedPosts($customer, $profile, $transaction);
                    $newLoyaltyCard = $this->checkLoyaltyProgram($customer, $profile, $transaction);
                    $this->sendEmailReceipt($customer, $profile, $transaction);
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
        $post = Post::findOrFail($request->id);
        $transaction->deal_id = $post->id;
        $transaction->products = $post->deal_item;
        $transaction->net_sales = $request->price;

        $tax = round(($profile->tax_rate / 10000) * $transaction->net_sales);
        $total = $transaction->net_sales + $tax;
        $transaction->tax = $tax;
        $transaction->total = $total;

        $profile->transactions()->save($transaction);
        $result = $this->createCharge($transaction, $customer, $profile);

        if ($result) {
            $transaction->paid = true;
            $transaction->status = 20;
            $transaction->redeemed = false;
            $transaction->save();

            return response()->json(['success' => 'Post Purchased'], 200);
        } else {
            $transaction->paid = false;
            $transaction->status = 1;
            $transaction->save();
            return response()->json(['error' => 'Unable to charge card.'], 400);
        }
    }

    private function createCharge($transaction, $customer, $profile) {
        SplashPayments\Utilities\Config::setTestMode(true);
        SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
        $result = new SplashPayments\txns(
            array (
                'merchant' => $profile->account->splashId,
                'type' => 1,
                'origin' => 2,
                'token' => $customer->customer_id,
                'first' => $customer->first_name,
                'last' => $customer->last_name,
                'total' => $transaction->total
            )
        );
        try {
            $result->create();
        }
        catch (SplashPayments\Exceptions\Base $e) {

        }
        if ($result->hasErrors()) {
            $success =  false;
            $err = $result->getErrors();
        } else {
            $data = $result->getResponse();
            $processedTransaction = $data[0];
            if ($processedTransaction->status == '0' || $processedTransaction->status == '1') {
                $success = true;
            } else {
                $success = false;
            }
            $transaction->splash_id = $processedTransaction->id;
            $transaction->save();
        }
        return $success;
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

    public function sendEmailReceipt($customer, $profile, $transaction) {
        $items = $transaction->products;
        $items = json_decode($items);

        return Mail::send('emails.receipt', ['items' => $items, 'profile' => $profile, 'transaction' => $transaction], function($m) use ($customer, $profile) {
            $m->from('receipts@pockeyt.com', 'Pockeyt Receipts');
            $m->to($customer->email, $customer->first_name)->subject('Recent transaction from Pockeyt');
      });
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
        $purchased = Transaction::where('deal_id', '=', $postId)->where('refunded', '=', false)->get();

        return response()->json($purchased);
    }

     public function getUserPurchases(Request $request) {
        $customerId = $request->customerId;
        $businessId = $request->businessId;
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subDays(7);

        $purchases = Transaction::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('profile_id', '=', $businessId)
                ->where('refund_full', '=', false)
                ->where('products', '!=', '');
        })->orderBy('created_at', 'desc')->take(5)->get();

        $lastPostViewed = PostAnalytic::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('business_id', '=', $businessId);
        })->orderBy('updated_at', 'desc')->first();
        if (!$lastPostViewed) {
            $lastViewedPost = $lastPostViewed;
        } else {
            $lastViewedPost = Post::findOrFail($lastPostViewed->post_id);
            $lastViewedPost['viewed_on'] = $lastPostViewed->updated_at;
        }

        $recentShared = PostAnalytic::where(function($query) use ($fromDate, $currentDate, $customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('business_id', '=', $businessId)
                ->whereBetween('shared_on', [$fromDate, $currentDate]);
        })->orderBy('shared_on', 'desc')->first();
        if (!$recentShared) {
            $recentSharedPost = $recentShared;
        } else {
            $recentSharedPost = Post::findOrFail($recentShared->post_id);
            $recentSharedPost['shared_on'] = $recentShared->shared_on;
        }

        $recentBookmarked = PostAnalytic::where(function($query) use ($fromDate, $currentDate, $customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('business_id', '=', $businessId)
                ->whereBetween('bookmarked_on', [$fromDate, $currentDate]);
        })->orderBy('bookmarked_on', 'desc')->first();
        if (!$recentBookmarked) {
            $recentBookmarkedPost = $recentBookmarked;
        } else {
            $recentBookmarkedPost = Post::findOrFail($recentBookmarked->post_id);
            $recentBookmarkedPost['bookmarked_on'] = $recentBookmarked->bookmarked_on;
        }

        return response()->json(array('purchases' => $purchases, 'lastViewedPost' => $lastViewedPost, 'recentSharedPost' => $recentSharedPost, 'recentBookmarkedPost' => $recentBookmarkedPost));
    }

    public function getUserDeals(Request $request) {
        $customerId = $request->customerId;
        $businessId = $request->businessId;

        $redeemableDeals = Transaction::where(function($query) use ($customerId, $businessId) {
            $query->where('user_id', '=', $customerId)
                ->where('profile_id', '=', $businessId)
                ->where('refunded', '=', false)
                ->where('redeemed', '=', false)
                ->where('paid', '=', true);     
        })->get();
        if (isset($redeemableDeals)) {
            $posts = [];
            foreach ($redeemableDeals as $deal) {
                $post = Post::findOrFail($deal->deal_id);
                array_push($posts, $post);
            }
            return response()->json(array('redeemableDeals' => $redeemableDeals, 'posts' => $posts));
        } else {
            return;
        }
    }

    public function redeemUserDeal(Request $request) {
        $transactionId = $request->dealId;
        $transaction = Transaction::findOrFail($transactionId);
        $post = Post::findOrFail($transaction->deal_id);

        $transaction->redeemed = true;
        $transaction->save();
        $message =  \PushNotification::Message('Deal redeemed at ' . $post->profile->business_name . ' for ' . $post->deal_item , 
            array(  'category' => 'default',
                    'locKey' => '1',
                    'custom' => array(
                        'inAppMessage' => 'Deal redeemed at ' . $post->profile->business_name . ' for ' . $post->deal_item
                    )
        ));
        $token = PushId::where('user_id', '=', $transaction->user_id)->first();
        if ($token->device_type === 'iOS') {
            $pushService = 'PockeytIOS';
        } else {
            $pushService = 'PockeytAndroid';
        }
        $collection = \PushNotification::app($pushService)
          ->to($token->push_token)
          ->send($message);
        return response('success');
    }

    public function getTransactions(Request $request) {
        $businessId = $request->businessId;
        $transactionsPending = Transaction::where(function($query) use ($businessId) {
            $query->where('profile_id', '=', $businessId)
                ->where('status', '<', '20')
                ->where('refund_full', '=', false);
        })->orderBy('status', 'asc')->get();

        $transactionsFinalized = Transaction::where(function($query) use ($businessId) {
            $query->where('profile_id', '=', $businessId)
                ->where('status', '=', '20')
                ->where('refund_full', '=', false);
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
                ->where('paid', '=', true)
                ->where('refund_full', '=', false);
        })->orderBy('created_at', 'desc')->paginate(10);

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
                        'updated_at' => $transaction->created_at,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
    }

    public function getDeals(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $paginator = Transaction::where('refunded', '=', false)
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

    public function receiveSquareTransaction(Request $request) {
        $squareLocationId = $request->location_id;
        $payment_id = $request->entity_id;

        $businessAccount = Account::where('square_location_id', '=', $squareLocationId)->first();
        $squareToken = $businessAccount->profile->square_token;
        try {
          $token = Crypt::decrypt($squareToken);
        } catch (DecryptException $e) {
          dd($e);
        }
        
        $client = new Client([
            'base_url' => ['https://connect.squareup.com/{version}/', ['version' => 'v1']]
        ]);

        try {
            $response = $client->get($squareLocationId . '/payments' . '/' . $payment_id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ]
            ]);
        } catch(RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
        }


        // $client = new \GuzzleHttp\Client(['base_uri' => 'https://connect.squareup.com/v1/']);
        // try {
        //   $response = $client->request('GET', $squareLocationId . '/payments' . '/' . $payment_id, [
        //     'headers' => [
        //       'Authorization' => 'Bearer ' . $token,
        //       'Accept' => 'application/json'
        //     ]
        //   ]);
        // } catch (RequestException $e) {
        //   if ($e->hasResponse()) {
        //     dd($e->getResponse());
        //   }
        // }



        $payment = json_decode($response->getBody());
        foreach ($payment->itemizations as $item) {
            if ($item->name == "Pockeyt Customer") {
                $customerId = str_replace('pockeyt', '', $item->item_detail->item_variation_id);
                return $this->processSquarePayment($payment, $businessAccount, $customerId);
            }
        }
    }

    public function processSquarePayment($payment, $businessAccount, $customerId) {
        $profile = $businessAccount->profile;
        $customer = User::findOrFail($customerId);
        $transaction = new Transaction;

        $transaction->tax = $payment->tax_money->amount;
        $transaction->net_sales = $payment->net_sales_money->amount;
        $transaction->tips = $payment->tip_money->amount;
        $transaction->total = $payment->total_collected_money->amount;
        $transaction->user_id = $customer->id;
        $transaction->paid = false;
        $transaction->status = 10;
        $transaction->employee_id = "empty";

        $products = [];
        foreach ($payment->itemizations as $item) {
            if ($item->name != 'Pockeyt Customer') {
                array_push($products, (object)[
                        "name" => $item->name . ', ' . $item->item_variation_name,
                        "quantity" => round($item->quantity),
                        "price" => $item->single_quantity_money->amount
                    ]
                );
            }
        }

        $transaction->products = json_encode($products);
        $profile->transactions()->save($transaction);
        event(new TransactionsChange($profile));
        return $this->confirmTransaction($transaction, $customer, $profile);
    }

    public function checkFirstTransaction($customer, $transaction) {
        if ($customer->new_customer) {
            $customer->new_customer = false;
            $transaction->customer_first_transaction = true;
            $customer->save();
            $transaction->save();
        }
        return;
    }

    public function issueRefund() {
        $profile = $this->user->profile;
        $transactions = Transaction::where(function($query) use ($profile) {
            $query->where('profile_id', '=', $profile->id)
                ->where('paid', '=', true)
                ->where('refund_full', '=', false);
        })->leftJoin('users', 'transactions.user_id', '=', 'users.id')->select('transactions.*', 'users.first_name', 'users.last_name', 'customer_id')->orderBy('transactions.updated_at', 'desc')->take(10)->get();
        return view('transactions.refund', compact('transactions', 'profile'));
    }

    public function searchRefunds(Request $request) {
        $searchSelection = $request->searchSelection;
        $searchInput = $request->searchInput;
        $businessId = $request->businessId;

        if ($searchSelection == 'Email') {
            $user = User::where('email', '=', $searchInput)->first();
            if ($user) {
                $userId = $user->id;
                $transactions = Transaction::where(function($query) use ($userId, $businessId) {
                    $query->where('profile_id', '=', $businessId)
                    ->where('user_id', '=', $userId)
                    ->where('refund_full', '=', false);
                })->leftJoin('users', 'transactions.user_id', '=', 'users.id')->select('transactions.*', 'users.first_name', 'users.last_name', 'customer_id')->orderBy('transactions.updated_at', 'desc')->get();
                if (count($transactions) > 0) {
                    return response()->json($transactions);
                } else {
                    return response('Not Found');
                }
            } else {
                return response('Not Found');
            }
        } else {
            $transactions = Transaction::where('splash_id', 'like', '%' . $searchInput)->leftJoin('users', 'transactions.user_id', '=', 'users.id')->select('transactions.*', 'users.first_name', 'users.last_name', 'customer_id')->orderBy('transactions.updated_at', 'desc')->get();
            if (count($transactions) > 0) {
                return response()->json($transactions);
            } else {
                return response('Not Found');
            }
        }
    }

    public function refundSubmitPartial(Request $request) {
        $refundedProducts = json_decode($request->products_new);
        foreach ($refundedProducts as $i => $product) {
            if ($product->quantity == 0) {
                unset($product[$i]);
            }
        }
        dd($refundedProducts);

        $transaction = Transaction::findOrFail($request->id);

        $refundAmount = $request->total_new;
        $profile = $this->user->profile;
        $transactionSplashId = $transaction->splash_id;
        $partial = true;
        $status = $this->checkTxnStatus($transactionSplashId);

        if ($status === "captured") {
            $result = $this->refundTransaction($refundAmount, $transactionSplashId, $partial, $transaction);
        } else {
            $result = $this->reverseAuth($transactionSplashId, $partial, $refundAmount, $transaction);
        }

        if ($result) {
            $transaction->refunded = true;
            $transaction->refund_full = false;
            $transaction->refund_amount = $refundAmount;
            $transaction->status = 30;
            $transaction->products = $request->products_old;
            $transaction->tax = $request->tax_old;
            $transaction->net_sales = $request->net_sales_old;
            $transaction->total = $request->total_old;
            $transaction->refund_products = $request->products_new;
            $transaction->refund_tax = $request->tax_new;
            $transaction->save();
            $customer = User::findOrFail($transaction->user_id);
            $this->sendEmailRefund($customer, $profile, $transaction);
            flash()->success('Success', 'Refund Complete');
        } else {
            $transaction->status = 31;
            $transaction->save();
            flash()->overlay('Unable to Refund', 'Please Contact Customer Support', 'error');
        }

        $transactions = Transaction::where(function($query) use ($profile) {
            $query->where('profile_id', '=', $profile->id)
                ->where('paid', '=', true)
                ->where('refund_full', '=', false);
        })->leftJoin('users', 'transactions.user_id', '=', 'users.id')->select('transactions.*', 'users.first_name', 'users.last_name', 'customer_id')->orderBy('transactions.updated_at', 'desc')->take(10)->get();
        return redirect()->route('transactions.refund', compact('transactions', 'profile'));
    }

    public function refundSubmitAll(Request $request) {
        $transaction = Transaction::findOrFail($request->id);

        $refundAmount = $transaction->total;
        $profile = $this->user->profile;
        $transactionSplashId = $transaction->splash_id;
        $partial = false;
        $status = $this->checkTxnStatus($transactionSplashId);

        if ($status === "captured") {
            $result = $this->refundTransaction($refundAmount, $transactionSplashId, $partial, $transaction);
        } else {
            $result = $this->reverseAuth($transactionSplashId, $partial, $refundAmount, $transaction);
        }

        if ($result) {
            $transaction->refunded = true;
            $transaction->refund_full = true;
            $transaction->refund_amount = $refundAmount;
            $transaction->refund_products = $transaction->products;
            $transaction->refund_tax = $transaction->tax;
            $transaction->status = 30;
            $transaction->save();
            $customer = User::findOrFail($transaction->user_id);
            $this->sendEmailRefund($customer, $profile, $transaction);
            flash()->success('Success', 'Refund Complete');
        } else {
            $transaction->status = 31;
            $transaction->save();
            flash()->overlay('Unable to Refund', 'Please Contact Customer Support', 'error');
        }

        $transactions = Transaction::where(function($query) use ($profile) {
            $query->where('profile_id', '=', $profile->id)
                ->where('paid', '=', true)
                ->where('refund_full', '=', false);
        })->leftJoin('users', 'transactions.user_id', '=', 'users.id')->select('transactions.*', 'users.first_name', 'users.last_name', 'customer_id')->orderBy('transactions.updated_at', 'desc')->take(10)->get();
        return redirect()->route('transactions.refund', compact('transactions', 'profile'));
    }

    private function checkTxnStatus($transactionSplashId) {
        SplashPayments\Utilities\Config::setTestMode(true);
        SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
        $result = new SplashPayments\txns(
            array (
                'id' => $transactionSplashId
            )
        );
         try {
            $result->retrieve();
        }
        catch (SplashPayments\Exceptions\Base $e) {

        }
        if ($result->hasErrors()) {
            $err = $result->getErrors();
            dd($err);
        } else {
            $data = $result->getResponse();
            $response = $data[0];
            if ($response->status == '3' || $response->status == '4') {
                $status = "captured";
            } elseif ($response->status == '0' || $response->status == '1') {
                $status = "pending";
            }
        }
        return $status;
    }

    private function refundTransaction($refundAmount, $transactionSplashId, $partial, $transaction) {
        SplashPayments\Utilities\Config::setTestMode(true);
        SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
        if ($partial) {
            $result = new SplashPayments\txns(
                array (
                    'fortxn' => $transactionSplashId,
                    'type' => '5',
                    'total' => $refundAmount
                )
            );
        } else {
            $result = new SplashPayments\txns(
                array (
                    'fortxn' => $transactionSplashId,
                    'type' => '5'
                )
            );
        }
        try {
            $result->create();
        }
        catch (SplashPayments\Exceptions\Base $e) {

        }
        if ($result->hasErrors()) {
            $err = $result->getErrors();
            dd($err);
        } else {
            $data = $result->getResponse();
            $response = $data[0];
            if ($response->status == '0' || $response->status == '3') {
                $transaction->refund_id = $response->id;
                $transaction->save();
                $success = true;
            } else {
                $success = false;
            }
        }
        return $success;
    }

    private function reverseAuth($transactionSplashId, $partial, $refundAmount, $transaction) {
        SplashPayments\Utilities\Config::setTestMode(true);
        SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
        if ($partial) {
            $result = new SplashPayments\txns(
                array (
                    'fortxn' => $transactionSplashId,
                    'type' => '4',
                    'total' => $refundAmount
                )
            );
        } else {
            $result = new SplashPayments\txns(
                array (
                    'fortxn' => $transactionSplashId,
                    'type' => '4'
                )
            );
        }
        try {
            $result->create();
        }
        catch (SplashPayments\Exceptions\Base $e) {

        }
        if ($result->hasErrors()) {
            $err = $result->getErrors();
            dd($err);
        } else {
            $data = $result->getResponse();
            $response = $data[0];
            if ($response->status == '0' || $response->status == '1') {
                $transaction->refund_id = $response->id;
                $transaction->save();
                $success = true;
            } else {
                $success = false;
            }
        }
        return $success;
    }

    public function sendEmailRefund($customer, $profile, $transaction) {
        $items = $transaction->refund_products;
        $items = json_decode($items);

        return Mail::send('emails.refund', ['items' => $items, 'profile' => $profile, 'transaction' => $transaction], function($m) use ($customer, $profile) {
            $m->from('refunds@pockeyt.com', 'Pockeyt Refunds');
            $m->to($customer->email, $customer->first_name)->subject('Refund from Pockeyt');
      });
    }
}







