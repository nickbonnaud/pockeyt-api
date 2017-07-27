<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRefundDataToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('refunded')->default(false)->after('total');
            $table->boolean('refund_full')->default(false)->after('refunded');
            $table->integer('refund_amount')->nullable()->unsigned()->after('refund_full');
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
            $table->dropColumn('refund_amount');
            $table->dropColumn('refund_full');
            $table->dropColumn('refunded');
        });
    }
}
