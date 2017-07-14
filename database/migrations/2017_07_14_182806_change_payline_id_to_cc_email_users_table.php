<?php

use Illuminate\Database\Migrations\Migration;

class ChangePaylineIdToCcEmailUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->renameColumn('payline_id', 'card_email');
        });
    }
}
