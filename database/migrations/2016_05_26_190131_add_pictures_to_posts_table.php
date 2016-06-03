<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPictureToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('photo_name')->nullable()->after('body');
            $table->string('photo_path')->nullable()->after('photo_name');
            $table->string('thumb_path')->nullable()->after('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('thumb_path');
            $table->dropColumn('photo_path');
            $table->dropColumn('photo_name');
        });
    }
}
