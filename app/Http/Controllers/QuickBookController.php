<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Profile;
use App\User;
use App\Account;
use App\Transaction;
use Carbon\Carbon;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class QuickBookController extends Controller
{

  private $IntuitAnywhere;
  private $context;
  private $realm;

  public function __construct(){
    if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
      // Initialize creates the neccessary database schema for queueing up requests and logging
      \QuickBooks_Utilities::initialize(env('QBO_DSN'));
    }
    $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));

    parent::__construct();
  }

  public function qboOpenId(Request $request) {
    $openid = new \LightOpenID('https://pockeyt-test.com');
    if (!$openid->mode) {
      $openid->identity = "https://openid.intuit.com/Identity-me";
      $openid->required = array('contact/email', 'namePerson');
      $openid->optional = array('namePerson/first', 'namePerson/last');
      header('Location: ' . $openid->authUrl());
    } else {
      $userData = $openid->getAttributes();
      $user = User::where('email', '=', $userData['contact/email'])->first();
      if ($user) {
        if (!$user->profile->connected_qb) {
          # code...
        }
      } else {
        $user = new User;
        $user->email = $userData['contact/email'];
        if ($user['namePerson/first']) {
          $user->first_name = $userData['namePerson/first'];
        } else {
          $user->first_name = $userData['namePerson'];
        }
        if ($user['namePerson/last']) {
          $user->last_name = $userData['namePerson/last'];
        } else {
          $user->last_name = "Please set";
        }

        $user->save();
        $profile = $user->publish(new Profile);
        $account = new Account;
        $profile->account()->save();
        dd("ok");
      }
    }
  }


  public function qboTax() {
    return view('qbo.tax');
  }
    
  public function  qboConnect(){
  	$the_tenant = $this->user->profile->id;

    if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), $the_tenant) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), $the_tenant)) {
      // Set up the IPP instance
      $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
      // Get our OAuth credentials from the database
      $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), $the_tenant);
      // Tell the framework to load some data from the OAuth store
      $IPP->authMode(
        \QuickBooks_IPP::AUTHMODE_OAUTH,
        env('QBO_USERNAME'),
        $creds);

      if (env('QBO_SANDBOX')) {
        // Turn on sandbox mode/URLs
        $IPP->sandbox(true);
      }
      // This is our current realm
      $this->realm = $creds['qb_realm'];
      // Load the OAuth information from the database
      $this->context = $IPP->context();
      return true;
    } else {
      return false;
    }
  }

  public function qboOauth(){
  	$the_tenant = $this->user->profile->id;
    if ($this->IntuitAnywhere->handle(env('QBO_USERNAME'), $the_tenant))
    {
      ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
    }
    else
    {
      // If this happens, something went wrong with the OAuth handshake
      die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
    }
  }

  public function qboSuccess(){
    $this->setTaxAccount();
     $taxCodeId = $this->user->profile->account->pockeyt_qb_taxcode;
    if (isset($taxCodeId)) {
      $this->setPockeytId();
      $this->createPockeytAccount();
      $this->createPockeytTipsAccount();
      $this->createPockeytItem();
      $this->createPockeytTipsItem();
      $this->createPockeytPaymentMethod();
      $this->setQbActive();
      return view('qbo.success');
    } else {
      $the_tenant = $this->user->profile->id;
      $account = $this->user->profile->account;
      $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), $the_tenant, true);
      flash()->overlay('Oops', 'Your current Sales Tax for Pockeyt Pay is ' . $this->user->profile->tax_rate / 100 . '%, based on your location. This does not match your Quickbooks Sales Tax. Please make sure your location is correct on Pockeyt or your Sales Tax on QuickBooks is correct, and try again', 'error');
      return redirect()->route('qbo.tax');;
    }
  }

  public function qboDisconnect(){
  	$the_tenant = $this->user->profile->id;
    $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), $the_tenant, true);
    return redirect()->intended("/yourpath");// afer disconnect redirect where you want
  }

  public function setPockeytId() {
  	$this->qboConnect();
  	$customerService = new \QuickBooks_IPP_Service_Customer();
  	$customer = new \QuickBooks_IPP_Object_Customer();

  	$customer->setDisplayName('Pockeyt Customer');
  	$customer->setNotes('Created to track Pockeyt sales');
  	if ($resp = $customerService->add($this->context, $this->realm, $customer)) {
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return $this->setQbId($resp);
  	} else {
  		print($customerService->lastError($this->context));
  	}
  }

  public function createPockeytAccount() {
  	$this->qboConnect();
  	$accountService = new \QuickBooks_IPP_Service_Account();
  	$account = new \QuickBooks_IPP_Object_Account();

  	$account->setName('Pockeyt Income');
  	$account->setDescription('Pockeyt Sales');
  	$account->setCashFlowClassification('Revenue');
  	$account->setAccountType('Income');
  	$account->setAccountSubType('SalesOfProductIncome');
  	
  	if ($resp = $accountService->add($this->context, $this->realm, $account))
		{
			$resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
			return $this->setQbAccount($resp);
		}
		else
		{
			print($accountService->lastError($this->context));
		}
  }

  public function createPockeytTipsAccount() {
    $this->qboConnect();
    $accountService = new \QuickBooks_IPP_Service_Account();
    $account = new \QuickBooks_IPP_Object_Account();

    $account->setName('Pockeyt Tips');
    $account->setCashFlowClassification('Liability');
    $account->setAccountType('OtherCurrentLiabilities');
    $account->setAccountSubType('OtherCurrentLiabilities');
    
    if ($resp = $accountService->add($this->context, $this->realm, $account))
    {
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return $this->setQbTipsAccount($resp);
    }
    else
    {
      print($accountService->lastError($this->context));
    }
  }

  public function createPockeytItem() {
  	$this->qboConnect();
  	$itemService = new \QuickBooks_IPP_Service_Item();
  	$item = new \QuickBooks_IPP_Object_Item();

  	$item->setName('Pockeyt Item');
		$item->setType('Service');
		$item->setIncomeAccountRef($this->user->profile->account->pockeyt_qb_account);
		if ($resp = $itemService->add($this->context, $this->realm, $item))
		{
			$resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return $this->setPockeytItem($resp);
		}
		else
		{
			print($itemService->lastError($this->context));
		}
  }

  public function createPockeytTipsItem() {
    $this->qboConnect();
    $itemService = new \QuickBooks_IPP_Service_Item();
    $item = new \QuickBooks_IPP_Object_Item();

    $item->setName('Pockeyt Tips');
    $item->setType('Service');
    $item->setIncomeAccountRef($this->user->profile->account->pockeyt_qb_tips_account);
    if ($resp = $itemService->add($this->context, $this->realm, $item))
    {
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return $this->setPockeytTipsItem($resp);
    }
    else
    {
      print($itemService->lastError($this->context));
    }
  }

  public function createPockeytPaymentMethod() {
  	$this->qboConnect();
  	$paymentMethodService = new \QuickBooks_IPP_Service_PaymentMethod();
  	$paymentMethod = new \QuickBooks_IPP_Object_PaymentMethod();

  	$paymentMethod->setName('Pockeyt Payment');
  	if ($resp = $paymentMethodService->add($this->context, $this->realm, $paymentMethod))
  	{
  		$resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return $this->setPockeytPaymentMethod($resp);
  	}
  	else
  	{
  		print($paymentMethodService->lastError($this->context));
  	}
  }

  public function setTaxAccount() {
    $this->qboConnect();
    
    $taxRateService = new \QuickBooks_IPP_Service_TaxRate();
    $taxRates = $taxRateService->query($this->context, $this->realm, "SELECT * FROM TaxRate");
    $TaxCodeService = new \QuickBooks_IPP_Service_TaxCode();
    $taxCodes = $TaxCodeService->query($this->context, $this->realm, "SELECT * FROM TaxCode");
    foreach ($taxCodes as $taxCode) {
      $taxRateList = $taxCode->getSalesTaxRateList();
      if ($taxRateList !== null) {
        $qbTaxRate = 0;

        $taxRateDetailLine = $taxRateList->countTaxRateDetail();
        for ($i = 0; $i < $taxRateDetailLine; $i++) {
          $taxRateDetail = $taxRateList->getTaxRateDetail($i);
          $taxRateRef = $taxRateDetail->getTaxRateRef();

          foreach ($taxRates as $taxRate) {
            $taxId = $taxRate->getId();
            if ($taxId == $taxRateRef) {
              $componentRate = floatval($taxRate->getRateValue());
              $qbTaxRate = $qbTaxRate + $componentRate;
            }
          }
        }
        $businessTaxRate = $this->user->profile->tax_rate / 100;
        if ($qbTaxRate == round($businessTaxRate, 2)) {
          $taxCodeId = $taxCode->getId();
          $taxCodeId = str_replace('{','',$taxCodeId);
          $taxCodeId = str_replace('}','',$taxCodeId);
          $taxCodeId = abs($taxCodeId);
          return $this->setPockeytTaxCode($taxCodeId);
        }
      }
    }
    return;
  }

  public function setQbActive() {
  	$profile = $this->user->profile;
    $profile->connected_qb = true;

    $account = $profile->account;
    $account->qb_connected_date = Carbon::now(new DateTimeZone(config('app.timezone')));

    $profile->save();
    return $account->save();
  }

  public function setQbId($resp) {
  	$account = $this->user->profile->account;
    $account->pockeyt_qb_id = $resp;
    return $account->save();
  }

  public function setQbAccount($resp) {
  	$account = $this->user->profile->account;
    $account->pockeyt_qb_account = $resp;
    return $account->save();
  }

  public function setQbTipsAccount($resp) {
    $account = $this->user->profile->account;
    $account->pockeyt_qb_tips_account = $resp;
    return $account->save();
  }

  public function setPockeytItem($resp) {
  	$account = $this->user->profile->account;
    $account->pockeyt_item = $resp;
    return $account->save();
  }

  public function setPockeytTipsItem($resp) {
    $account = $this->user->profile->account;
    $account->pockeyt_tips_item = $resp;
    return $account->save();
  }

  public function setPockeytPaymentMethod($resp) {
  	$account = $this->user->profile->account;
  	$account->pockeyt_payment_method = $resp;
  	return $account->save();
  }

  public function setPockeytTaxCode($taxCodeId) {
    $account = $this->user->profile->account;
    $account->pockeyt_qb_taxcode = $taxCodeId;
    return $account->save();
  }
}






