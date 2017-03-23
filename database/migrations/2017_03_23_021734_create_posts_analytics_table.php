<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('business_id')->unsigned();
            $table->integer('post_id')->unsigned();
            $table->boolean('viewed')->default(false);
            $table->dateTime('viewed_on')->nullable();
            $table->boolean('shared')->default(false);
            $table->dateTime('shared_on')->nullable();
            $table->boolean('bookmarked')->default(false);
            $table->dateTime('bookmarked_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts_analytics');
    }
}
