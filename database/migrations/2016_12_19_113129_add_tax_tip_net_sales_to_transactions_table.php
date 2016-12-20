<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxTipNetSalesToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('tax')->nullable()->after('products');
            $table->integer('tips')->nullable()->after('tax');
            $table->integer('net_sales')->nullable()->after('tips');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('net_sales');
            $table->dropColumn('tips');
            $table->dropColumn('tax');
        });
    }
}
