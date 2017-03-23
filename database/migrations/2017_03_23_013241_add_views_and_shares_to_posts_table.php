<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewsAndSharesToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('views')->default(0)->after('insta_post_id');
            $table->integer('shares')->default(0)->after('views');
            $table->integer('bookmarks')->default(0)->after('shares');
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
            $table->dropColumn('bookmarks');
            $table->dropColumn('shares');
            $table->dropColumn('views');
        });
    }
}
