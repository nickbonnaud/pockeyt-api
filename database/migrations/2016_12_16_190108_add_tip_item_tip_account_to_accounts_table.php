<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipItemTipAccountToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('pockeyt_qb_tips_account')->nullable()->after('pockeyt_payment_method');
            $table->integer('pockeyt_tips_item')->nullable()->after('pockeyt_qb_tips_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('pockeyt_tips_item');
            $table->dropColumn('pockeyt_qb_tips_account');
        });
    }
}
