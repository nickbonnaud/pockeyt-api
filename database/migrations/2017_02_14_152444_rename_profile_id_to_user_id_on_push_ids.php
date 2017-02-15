<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameProfileIdToUserIdOnPushIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_ids', function ($table) {
            $table->renameColumn('profile_id', 'user_id');
        });
    }
}
