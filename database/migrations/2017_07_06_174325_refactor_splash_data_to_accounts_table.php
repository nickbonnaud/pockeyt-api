<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorSplashDataToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->renameColumn('accountNumber4', 'accountNumber');
            $table->renameColumn('routingNumber4', 'routing');
            $table->renameColumn('last4', 'ssn');
        });
    }
}
