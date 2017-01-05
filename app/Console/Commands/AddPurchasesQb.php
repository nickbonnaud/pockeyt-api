<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Profile;
use App\Transaction;
use DateTimeZone;
use Carbon\Carbon;

class AddPurchasesQb extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:add-purchases-qb';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sync purchases with QuickB';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
    	// Initialize creates the neccessary database schema for queueing up requests and logging
    	\QuickBooks_Utilities::initialize(env('QBO_DSN'));
    }
    $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));

  	parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
  	$businesses = Profile::where('connected_qb', '=', true)->get();
    foreach ($businesses as $business) {
    	$the_tenant = $business->id;

    	if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), $the_tenant) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), $the_tenant)) {
    		$IPP = new \QuickBooks_IPP(env('QBO_DSN'));
    		$creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), $the_tenant);
    		$IPP->authMode(\QuickBooks_IPP::AUTHMODE_OAUTH, env('QBO_USERNAME'), $creds);

    		if (env('QBO_SANDBOX')) {
    			$IPP->sandbox(true);
    		}
    		$this->realm = $creds['qb_realm'];
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
        	$taxDetail->setTxnTaxCodeRef($business->account->pockeyt_qb_taxcode);
        	$taxDetail->setTotalTax($transaction->tax / 100);

        	$invoice->addTxnTaxDetail($taxDetail);
        	$invoice->setCustomerRef($business->account->pockeyt_qb_id);
        	if ($resp = $invoiceService->add($this->context, $this->realm, $invoice)) {
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
			  		if ($resp = $paymentService->add($this->context, $this->realm, $payment)) {
			  			$transaction->qb_synced = true;
							$transaction->save();
			  		} else {
			  			$transaction->qb_synced = false;
			  			dd($paymentService->lastError());
			  		}
        	} else {
        		$transaction->qb_synced = false;
        		dd($invoiceService->lastError());
        	}
    		}
    	}
    }
  }
}
