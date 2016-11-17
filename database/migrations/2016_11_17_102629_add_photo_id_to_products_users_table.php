<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoIdToProductsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('photo_id')->nullable()->after('product_photo_path');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('photo_id')->nullable()->after('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('photo_id');
        });
    }
}
