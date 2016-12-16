<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPockeytQbIdToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('pockeyt_qb_id')->nullable()->after('status');
            $table->integer('pockeyt_qb_account')->nullable()->after('pockeyt_qb_id');
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
            $table->dropColumn('pockeyt_qb_account');
            $table->dropColumn('pockeyt_qb_id');
        });
    }
}
