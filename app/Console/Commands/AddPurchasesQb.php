<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Profile;
use App\Transaction;
use DateTimeZone;

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
      $businesses = Profile::where('connected_qb', '=', true);

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
		      
		      $unSynchedTransactions = Transaction::where(function($query) use($the_tenant) {
		      	$query->where('profile_id', '=', $the_tenant)
		      		->where('qb_synced', '=', false);
		      })->get();

		      foreach ($$unSynchedTransactions as $transaction) {
		      	$invoiceService = new \QuickBooks_IPP_Service_Invoice();
						$invoice = new \QuickBooks_IPP_Object_Invoice();
		      	$invoice->setDueDate(date('Y-m-d', $transaction->updated_at));
		      	$invoice->setPrivateNote('Pockeyt Sale Transaction ID # ' . $transaction->id);

		      	$line = new \QuickBooks_IPP_Object_Line();
		      	$line->setDetailType('SalesItemLineDetail');
		      	$line->setAmount(($transaction->total / 100));
		      	$line->setDescription('Custom Amount');

		      	$salesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
 						$salesItemLineDetail->setUnitPrice(($transaction->total / 100));
 						$salesItemLineDetail->setQty(1);
 						$salesItemLineDetail->setItemRef($business->account->pockeyt_item);

 						$line->addSalesItemLineDetail($salesItemLineDetail);
 						$invoice->addLine($line);

 						$invoice->setCustomerRef($business->account->pockeyt_qb_id);
 						if ($resp = $invoiceService->add($this->context, $this->realm, $invoice))
				    {
				      $paymentService = new \QuickBooks_IPP_Service_Payment();
							$payment = new \QuickBooks_IPP_Object_Payment();

							$payment->setTotalAmt(($transaction->total / 100));
							$payment->setPrivateNote('Pockeyt Credit Card Payment. Pockeyt Transaction ID # ' . $transaction->id);
							$payment->setPaymentRefNum('transaction id');
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
							}
							else
							{
								$transaction->qb_synced = false;
								$transaction->save();
							}
				    }
		      }
		    }
      }
    }
}
