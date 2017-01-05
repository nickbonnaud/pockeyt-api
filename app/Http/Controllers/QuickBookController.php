<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Profile;
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
    if (isset($taxCode)) {
      // $this->setPockeytId();
      // $this->createPockeytAccount();
      // $this->createPockeytTipsAccount();
      // $this->createPockeytItem();
      // $this->createPockeytTipsItem();
      // $this->createPockeytPaymentMethod();
      // $this->setQbActive();
      return view('qbo.success');
    } else {
      $the_tenant = $this->user->profile->id;
      $account = $this->user->profile->account;
      $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), $the_tenant, true);
      flash()->overlay('Oops', 'Your Current Sales Tax for Pockeyt Pay based on your business location is ' . $this->user->profile->tax_rate / 100 . '%. This Does Not match your Quickbooks Sales Tax. Please make sure your location is correct on Pockeyt or your Sales Tax on QuickBooks is correct', 'error');
      return redirect()->route('accounts.edit', compact('account'));;
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
  		dd("done");
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return $this->setQbId($resp);
  	} else {
      dd("done");
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

  public function syncInvoice() {

    $businesses = Profile::where('connected_qb', '=', true)->get();

    foreach ($businesses as $business) {

    	$the_tenant = $business->id;

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

	      $account = $business->account;
	      $unSynchedTransactions = Transaction::where(function($query) use ($the_tenant, $account) {
	      	$query->where('qb_synced', '=', false)
	      		->where('profile_id', '=', $the_tenant)
	      		->where('created_at', '>', $account->qb_connected_date);
	      })->get();

	      foreach ($unSynchedTransactions as $transaction) {
	      	$invoiceService = new \QuickBooks_IPP_Service_Invoice();
					$invoice = new \QuickBooks_IPP_Object_Invoice();
					$invoice->setTxnDate($transaction->created_at->toDateString());
	      	$invoice->setDueDate($transaction->created_at->toDateString());
	      	$invoice->setPrivateNote('Pockeyt Sale Transaction ID # ' . $transaction->id);

	      	$line = new \QuickBooks_IPP_Object_Line();
	      	$line->setDetailType('SalesItemLineDetail');
	      	$line->setAmount(($transaction->net_sales / 100));
	      	$line->setDescription('Custom Amount');

	      	$salesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
					$salesItemLineDetail->setUnitPrice(($transaction->net_sales / 100));
					$salesItemLineDetail->setQty(1);
					$salesItemLineDetail->setItemRef($business->account->pockeyt_item);

          $salesItemLineDetail->setTaxCodeRef('TAX');

					$line->addSalesItemLineDetail($salesItemLineDetail);
					$invoice->addLine($line);

          if (isset($transaction->tips)) {
            $line = new \QuickBooks_IPP_Object_Line();
            $line->setDetailType('SalesItemLineDetail');
            $line->setAmount(($transaction->tips / 100));
            $line->setDescription('Pockeyt Tips Money');

            $salesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
            $salesItemLineDetail->setUnitPrice(($transaction->tips / 100));
            $salesItemLineDetail->setQty(1);
            $salesItemLineDetail->setItemRef($business->account->pockeyt_tips_item);

            $line->addSalesItemLineDetail($salesItemLineDetail);
            $invoice->addLine($line);
          }

          $taxDetail = new \QuickBooks_IPP_Object_TxnTaxDetail();
          $taxDetail->setTxnTaxCodeRef('TAX');
          $taxDetail->setTotalTax($transaction->tax / 100);

          $taxLine = new \QuickBooks_IPP_Object_TaxLine();
          $taxLine->setAmount($transaction->tax / 100);
          $taxLine->setDetailType('TaxLineDetail');

          $taxLineDetail = new \QuickBooks_IPP_Object_TaxLineDetail();
          $taxLineDetail->setTaxRateRef(0);
          $taxLineDetail->setPercentBased(true);
          $taxLineDetail->setTaxPercent(7.5);
          $taxLineDetail->setNetAmountTaxable($transaction->net_sales);

          $taxLine->addTaxLineDetail($taxLineDetail);
          $taxDetail->addTaxLine($taxLine);

          $invoice->addTxnTaxDetail($taxDetail);

					$invoice->setCustomerRef($business->account->pockeyt_qb_id);
					if ($resp = $invoiceService->add($this->context, $this->realm, $invoice))
			    {
			      $paymentService = new \QuickBooks_IPP_Service_Payment();
						$payment = new \QuickBooks_IPP_Object_Payment();

						$payment->setTotalAmt(($transaction->total / 100));
						$payment->setTxnDate($transaction->created_at->toDateString());
						$payment->setPrivateNote('Pockeyt Credit Card Payment. Pockeyt Transaction ID # ' . $transaction->id);
						$payment->setPaymentRefNum($transaction->id);
						$payment->setPaymentMethodRef($business->account->pockeyt_payment_method);

						$line = new \QuickBooks_IPP_Object_Line();
						$line->setAmount(($transaction->total / 100));

						$linkedTxn = new \QuickBooks_IPP_Object_LinkedTxn();
				  	$linkedTxn->setTxnId($resp);
				  	$linkedTxn->setTxnType('Invoice');

				  	$line->setLinkedTxn($linkedTxn);

				  	$payment->addLine($line);
				  	$payment->setCustomerRef($business->account->pockeyt_qb_id);
				  	if ($resp = $paymentService->add($this->context, $this->realm, $payment))
						{
							$transaction->qb_synced = true;
							$transaction->save();
              $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
              $invoices = $InvoiceService->query($this->context, $this->realm, "SELECT * FROM Invoice STARTPOSITION 1 MAXRESULTS 10");
              dd($invoices);
						}
						else
						{
              dd($paymentService->lastError());
						}
			    } else
          {
            dd($invoiceService->lastError());
          }
	      }
	    }
    }
  }

  public function payInvoice($resp) {
  	$paymentService = new \QuickBooks_IPP_Service_Payment();
  	$payment = new \QuickBooks_IPP_Object_Payment();
  	$payment->setTotalAmt(10);
  	$payment->setPrivateNote('Pockeyt Credit Card Payment. Pockeyt Transaction ID # CHANGE');
  	$payment->setPaymentRefNum('transaction id');
  	$payment->setPaymentMethodRef($this->user->profile->account->pockeyt_payment_method);

  	$line = new \QuickBooks_IPP_Object_Line();
  	$line->setAmount(10);

  	$linkedTxn = new \QuickBooks_IPP_Object_LinkedTxn();
  	$linkedTxn->setTxnId($resp);
  	$linkedTxn->setTxnType('Invoice');

  	$line->setLinkedTxn($linkedTxn);

  	$payment->addLine($line);
  	$payment->setCustomerRef($this->user->profile->account->pockeyt_qb_id);
  	if ($resp = $paymentService->add($this->context, $this->realm, $payment))
		{
			print('Our new Payment ID is: [' . $resp . ']');
		}
		else
		{
			print($paymentService->lastError());
		}
  }

}






