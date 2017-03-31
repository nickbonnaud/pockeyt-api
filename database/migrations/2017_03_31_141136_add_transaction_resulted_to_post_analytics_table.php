<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionResultedToPostAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_analytics', function (Blueprint $table) {
            $table->boolean('transaction_resulted')->default(false)->after('bookmarked_on');
            $table->dateTime('transaction_on')->nullable()->after('transaction_resulted');
            $table->integer('total_revenue')->default(0)->after('transaction_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_analytics', function (Blueprint $table) {
            $table->dropColumn('total_revenue');
            $table->dropColumn('transaction_on');
            $table->dropColumn('transaction_resulted');
        });
    }
}
