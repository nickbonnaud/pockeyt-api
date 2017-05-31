<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPockeytLiteDataToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('square_category_id')->nullable()->after('square_location_id');
            $table->string('square_item_id')->nullable()->after('square_category_id');
            $table->boolean('pockeyt_lite_enabled')->default(false)->after('square_item_id');
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
            $table->dropColumn('pockeyt_lite_enabled');
            $table->dropColumn('square_item_id');
            $table->dropColumn('square_category_id');
        });
    }
}
